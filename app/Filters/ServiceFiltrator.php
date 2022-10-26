<?php


namespace App\Filters;

use App\Http\Requests\Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceFiltrator
{

    public function apply(Builder $queryBuilder, Request $request, $ignoreSearchTerms = false)
    {
        if ($request->filled('start_date')) {
            $queryBuilder->whereHas(
                'schedules',
                function ($query) use ($request) {
                    $date = Carbon::parse(urldecode($request->start_date))->toDateTimeString();
                    $query->where('start_date', '>=', $date);
                }
            );
        }
        if ($request->filled('end_date')) {
            $queryBuilder->whereHas(
                'schedules',
                function ($query) use ($request) {
                    $date = Carbon::parse(urldecode($request->end_date))->toDateTimeString();
                    $query->where('end_date', '<=', $date);
                }
            );
        }

        if ($request->filled('excluded')) {
            $excludedId = (int)$request->get('excluded');
            $queryBuilder->where('services.id', '<>', $excludedId);
        }

        if ($request->filled('date_after')) {
            $queryBuilder->whereHas(
                'schedules',
                function ($query) use ($request) {
                    $date = Carbon::parse(urldecode($request->date_after))->toDateTimeString();
                    $query->where('start_date', '>=', $date);
                }
            );
        }

        $serviceTypes = $request->getArrayFromRequest('service_type');
        if (!empty($serviceTypes) && !in_array('all', $serviceTypes)) {
            $queryBuilder->whereIn('services.service_type_id', array_values($serviceTypes));
        }

        $isPublished = $request->getBoolFromRequest('is_published');
        if ($isPublished !== null) {
            $queryBuilder->where('services.is_published', $isPublished);
        }

        $practitioners = $request->getArrayFromRequest('practitioners');
        if (!empty($practitioners) && is_array($practitioners)) {
            $ignoreSearchTerms = true;
            $queryBuilder->whereIn('services.user_id', $practitioners);
        }

        if ($request->getBoolFromRequest('for_free') !== null) {
            $queryBuilder->whereHas(
                'schedules',
                function ($query) use ($request) {
                    $query->whereHas(
                        'prices',
                        function ($q) use ($request) {
                            $q->where('is_free', $request->getBoolFromRequest('for_free'));
                        }
                    );
                }
            );
        }

        if ($request->has('starting_soon')) {
            $queryBuilder->whereHas(
                'schedules',
                function ($query) {
                    $nextWeek = Carbon::now()->addDays(7)->toDateString();
                    $query->where('start_date', '>=', NOW())->orWhere('start_date', '<=', $nextWeek);
                }
            );
        }

        if ($request->filled('discipline_id') || $request->filled('disciplineId')) {
            $disciplineId = $request->filled('discipline_id') ? (int)$request->get('discipline_id') : (int)$request->get('disciplineId');
            $queryBuilder->whereHas(
                'disciplines',
                static function ($q) use ($disciplineId) {
                    $q->where('disciplines.id', $disciplineId);
                }
            );
        }

        if ($request->getBoolFromRequest('online')) {
            $queryBuilder->whereHas(
                'schedules',
                function ($query) {
                    $query->where('appointment', 'virtual');
                }
            );
        }

        $selectFields = [
            'services.*',
        ];

        if (!$ignoreSearchTerms) {
            $searchTerms = null;
            if ($request->filled('search')) {
                $searchTerms = $request->get('search');
            } elseif ($request->filled('q')) {
                $searchTerms = $request->get('q');
            }

            // default sorting
            if ($searchTerms === null) {
                $queryBuilder->join('users', 'users.id', '=', 'services.user_id')->leftJoin(
                    'plans',
                    'plans.id',
                    '=',
                    'users.plan_id'
                );

                $queryBuilder->leftJoin(
                    'schedules',
                    static function ($leftJoin) {
                        $leftJoin->on('services.id', '=', 'schedules.service_id')->where('schedules.is_published', '=', 1);
                    }
                );

                $selectFields[] = 'plans.price as price';
                $selectFields[] = 'plans.is_free as free_price';

                $queryBuilder->orderByRaw("FIELD(schedules.is_published , '1', '0') DESC")
                    ->orderByRaw('CASE WHEN schedules.start_date > NOW() THEN 0 ELSE 1 END, schedules.start_date')
                    ->orderByRaw("FIELD(plans.name , 'Practitioner', 'With trial', 'Free title') DESC")
                    ->orderBy('plans.price', 'DESC')
                    ->orderBy('plans.is_free', 'DESC');
            } else {
                // search terms
                $searchString = '%' . $searchTerms . '%';

                $queryBuilder->where(
                    function ($query) use ($searchString) {
                        $query->whereHas(
                            'focus_areas',
                            static function ($fQuery) use ($searchString) {
                                $fQuery->where('focus_areas.name', 'LIKE', $searchString);
                            }
                        )->orWhereHas(
                            'disciplines',
                            static function ($dQuery) use ($searchString) {
                                $dQuery->where('disciplines.name', 'LIKE', $searchString);
                            }
                        )->orWhereHas(
                            'keywords',
                            static function ($dQuery) use ($searchString) {
                                $dQuery->where('keywords.title', 'LIKE', $searchString);
                            }
                        )->orWhereHas(
                            'schedules',
                            static function ($dQuery) use ($searchString) {
                                $dQuery->where('schedules.city', 'LIKE', $searchString)->where(
                                    'schedules.is_published',
                                    true
                                );
                            }
                        )->orWhere('services.title', 'LIKE', $searchString)->orWhere(
                            'services.service_type_id',
                            'LIKE',
                            $searchString
                        )->orWhere('services.introduction', 'LIKE', $searchString)->orWhere(
                            'services.description',
                            'LIKE',
                            $searchString
                        );
                    }
                );

                $selectFields[] = "MATCH (focus_areas.name)
                        AGAINST ('{$searchTerms}' IN BOOLEAN MODE) AS rel_focus";
                $queryBuilder->orderBy('rel_focus', 'desc');
                $queryBuilder->leftJoin(
                    'focus_area_service as f_service',
                    function ($q) {
                        $q->on('f_service.service_id', '=', 'services.id');
                    }
                )->leftJoin(
                    'focus_areas',
                    function ($q) {
                        $q->on('f_service.focus_area_id', '=', 'focus_areas.id');
                    }
                );

                $selectFields[] = "MATCH (disciplines.name)
                        AGAINST ('{$searchTerms}' IN BOOLEAN MODE) AS rel_dis";
                $queryBuilder->orderBy('rel_dis', 'desc');
                $queryBuilder->leftJoin(
                    'discipline_service as d_service',
                    function ($q) {
                        $q->on('d_service.service_id', '=', 'services.id');
                    }
                )->leftJoin(
                    'disciplines',
                    function ($q) {
                        $q->on('d_service.discipline_id', '=', 'disciplines.id');
                    }
                );

                $selectFields[] = "MATCH (services.title)
                        AGAINST ('{$searchTerms}' IN BOOLEAN MODE) AS rel_title";
                $queryBuilder->orderBy('rel_title', 'desc');

                $selectFields[] = "MATCH (keywords.title)
                        AGAINST ('{$searchTerms}' IN BOOLEAN MODE) AS rel_key";
                $queryBuilder->orderBy('rel_key', 'desc');
                $queryBuilder->leftJoin(
                    'keyword_service as k_service',
                    function ($q) {
                        $q->on('k_service.service_id', '=', 'services.id');
                    }
                )->leftJoin(
                    'keywords',
                    function ($q) {
                        $q->on('k_service.keyword_id', '=', 'keywords.id');
                    }
                );

                $selectFields[] = "MATCH (services.introduction)
                        AGAINST ('{$searchTerms}' IN BOOLEAN MODE) AS rel_introduction";
                $selectFields[] = "MATCH (services.description)
                        AGAINST ('{$searchTerms}' IN BOOLEAN MODE) AS rel_description";
                $queryBuilder->orderBy('rel_introduction', 'desc');
                $queryBuilder->orderBy('rel_description', 'desc');
            }
            $queryBuilder->groupBy('services.id');
        }
        $queryBuilder->selectRaw(implode(', ', $selectFields));

        return $queryBuilder;
    }

}
