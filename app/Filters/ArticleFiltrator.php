<?php

namespace App\Filters;


use App\Http\Requests\Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;


class ArticleFiltrator {


    /**
     * @param \Illuminate\Database\Eloquent\Builder $queryBuilder
     * @param \App\Http\Requests\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Builder $queryBuilder, Request $request): Builder {

        if ($request->filled('search')) {
            $search = '%' . $request->get('search') . '%';
            $queryBuilder->where(function($query) use ($search) {
                $query->where('title', 'LIKE', $search)->orWhereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('first_name', 'LIKE', $search)
                              ->orWhere('last_name', 'LIKE', $search)
                              ->orWhere('business_name', 'LIKE', $search);
                });
            });
        }

        if ($request->filled('published_from')) {
            $queryBuilder->where('created_at', '>=', Carbon::parse($request->published_from)->startOfDay());
        }

        if ($request->filled('published_to')) {
            $queryBuilder->where('created_at', '<=', Carbon::parse($request->published_to)->endOfDay());
        }

        // Or Condition
        $publishedVariants = $request->getArrayFromRequest('is_published');
        $isDeleted = $request->getBoolFromRequest('is_deleted');

        if (($isDeleted === null && count($publishedVariants) === 0)
            || ($isDeleted === true && count($publishedVariants) === 2)) {
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

        $practitioners = $request->getArrayFromRequest('practitioner');
        if (count($practitioners)) {
            $queryBuilder->whereHas('user', function($query) use ($practitioners) {
                $query->whereIn('id', $practitioners);
            });
        }

        return $queryBuilder;
    }
}
