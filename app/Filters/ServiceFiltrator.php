<?php


namespace App\Filters;

use App\Http\Requests\Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class ServiceFiltrator {

    public function apply(Builder $queryBuilder, Request $request) {
        if ($request->filled('start_date')) {
            $queryBuilder->whereHas('schedules', function($query) use ($request) {
                $date = Carbon::parse(urldecode($request->start_date))->toDateTimeString();
                $query->where('start_date', '>=', $date);
            });
        }
        if ($request->filled('end_date')) {
            $queryBuilder->whereHas('schedules', function($query) use ($request) {
                $date = Carbon::parse(urldecode($request->end_date))->toDateTimeString();
                $query->where('end_date', '<=', $date);
            });
        }

        if ($request->filled('excluded')) {
            $excludedId = (int)$request->get('excluded');
            $queryBuilder->where('id', '<>', $excludedId);
        }

        if ($request->filled('date_after')) {
            $queryBuilder->whereHas('schedules', function($query) use ($request) {
                $date = Carbon::parse(urldecode($request->date_after))->toDateTimeString();
                $query->where('start_date', '>=', $date);
            });
        }

        if ($request->filled('id')) {
            $queryBuilder->whereHas('schedules.locations', function($query) use ($request) {
                $query->where('id', '=', $request->id);
            });
        }

        $serviceTypes = $request->getArrayFromRequest('service_type');
        if (!empty($serviceTypes)) {
            $queryBuilder->whereIn('service_type_id', array_values($serviceTypes));
        }

        $isPublished = $request->getBoolFromRequest('is_published');
        if ($isPublished !== null) {
            $queryBuilder->where('services.is_published', $isPublished);
        }

        $practitioners = $request->getArrayFromRequest('practitioners');
        if (!empty($practitioners)) {
            $queryBuilder->whereIn('services.user_id', $practitioners);
        }

        $searchString = $request->get('search');

        if ($searchString) {
            $queryBuilder->where('services.title', 'like', "%{$searchString}%");
        }

        $sortBy = $request->get('sortby');
        $queryBuilder->when($request->filled('sortby'), static function(Builder $query) use ($sortBy, $searchString) {
            switch ($sortBy) {
                case 'schedule':
                    $query->selectRaw('*, DATEDIFF(start_date, now()) as dif')
                          ->join('schedules', 'services.id', '=', 'schedules.service_id')
                          ->orderByRaw('ABS(dif)');
                    break;
                case 'service-introduction':
                    $query->selectRaw("MATCH (introduction)
                        AGAINST ('{$searchString}' IN BOOLEAN MODE) AS rel");
                    $query->orderBy('rel', 'desc');
                    break;
                case 'service-description':
                    $query->selectRaw("MATCH (description)
                        AGAINST ('{$searchString}' IN BOOLEAN MODE) AS rel");
                    $query->orderBy('rel', 'desc');
                    break;
                default:
                    break;
            }
        });

        if ($request->getBoolFromRequest('for_free') !== null) {
            $queryBuilder->whereHas('schedules', function($query) use ($request) {
                $query->whereHas('prices', function($q) use ($request) {
                    $q->where('is_free', $request->getBoolFromRequest('for_free'));
                });
            });
        }

        if ($request->has('starting_soon')) {
            $queryBuilder->whereHas('schedules', function($query) {
                $nextWeek = Carbon::now()->addDays(7)->toDateString();
                $query->where('start_date', '>=', NOW())
                ->orWhere('start_date', '<=', $nextWeek);
            });
        }

        if ($request->filled('discipline_id')) {
            $queryBuilder->whereHas('disciplines', function($q) use ($request){
                $q->where('discipline_id', $request->discipline_id);
            });
        }

        if ($request->getBoolFromRequest('online')) {
            $queryBuilder->whereHas('schedules', function($query) {
                $query->where('appointment', 'virtual');
            });
        }

        return $queryBuilder;
    }

}
