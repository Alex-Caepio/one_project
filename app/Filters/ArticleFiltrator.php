<?php

namespace App\Filters;


use App\Http\Requests\Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;


class ArticleFiltrator {


    /**
     * @param \Illuminate\Database\Eloquent\Builder $queryBuilder
     * @param \App\Http\Requests\Request $request
     * @param bool $frontView
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Builder $queryBuilder, Request $request, $frontView = false): Builder {

        $searchTerms = null;
        if ($request->filled('search')) {
            $searchTerms = $request->get('search');
        } else if ($request->filled('q')) {
            $searchTerms = $request->get('q');
        }

        if ($searchTerms !== null) {
            $search = '%' . $searchTerms . '%';
            $queryBuilder->where(function($query) use ($search) {
                $query->whereHas('focus_areas', function($focusQuery) use ($search) {
                    $focusQuery->where('name', 'LIKE', $search);
                })->orWhereHas('disciplines', function($dQuery) use ($search) {
                    $dQuery->where('name', 'LIKE', $search);
                })->orWhere('title', 'LIKE', $search)
                  ->orWhere('introduction', 'LIKE', $search)
                  ->orWhere('description', 'LIKE', $search)
                  ->orWhereHas('user', function($userQuery) use ($search) {
                        $userQuery->where('first_name', 'LIKE', $search)->orWhere('last_name', 'LIKE', $search)
                                  ->orWhere('business_name', 'LIKE', $search);
                    })
                  ->orWhereHas('keywords', function($kQuery) use ($search) {
                      $kQuery->where('title', 'LIKE', $search);
                    });
            });
        } else {
            $queryBuilder->orderByDesc('id');
        }

        /* Disciplines */
        if ($request->filled('discipline_id') || $request->filled('disciplineId')) {
            $disciplineId = $request->filled('discipline_id') ? $request->get('discipline_id') : $request->get('disciplineId');
            $queryBuilder->whereHas('disciplines', function($q) use ($disciplineId) {
                $q->where('disciplines.id', (int)$disciplineId);
            });
        }

        if ($request->filled('published_from')) {
            $date = Carbon::parse(urldecode($request->published_from))->startOfDay();
            $queryBuilder->where('created_at', '>=', $date);
        }

        if ($request->filled('published_to')) {
            $date = Carbon::parse(urldecode($request->published_from))->endOfDay();
            $queryBuilder->where('created_at', '<=', $date);
        }

        // Or Condition
        $publishedVariants = $request->getArrayFromRequest('is_published');
        $isDeleted = $request->getBoolFromRequest('is_deleted');

        if (!$frontView) {
            if (($isDeleted === null && count($publishedVariants) === 0) ||
                ($isDeleted === true && count($publishedVariants) === 2)) {
                $queryBuilder->withTrashed();
            } elseif (count($publishedVariants) === 1) {
                $isPublished = $request->getBoolValue($publishedVariants[0]);
                if ($isDeleted) {
                    $queryBuilder->where(function($query) use ($isPublished) {
                        $query->where('is_published', $isPublished)->orWhereNotNull('deleted_at');
                    })->withTrashed();
                } else {
                    $queryBuilder->where('is_published', $isPublished);
                }
            } elseif ($isDeleted === true && !count($publishedVariants)) {
                $queryBuilder->onlyTrashed();
            }
        }

        $practitioners = $request->getArrayFromRequest('practitioners');
        if (count($practitioners)) {
            $queryBuilder->whereHas('user', function($query) use ($practitioners) {
                $query->whereIn('id', $practitioners);
            });
        }

        return $queryBuilder;
    }
}
