<?php

namespace App\Filters;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ArticleFiltrator {

    public function apply(Builder $queryBuilder, Request $request) {

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
        $publishedVariants = $request->filled('is_published') ? explode(',', $request->get('is_published')) : [];
        $isDeleted = $request->filled('is_deleted') ? filter_var($request->get('is_deleted'), FILTER_VALIDATE_BOOLEAN) : null;

        if ($isDeleted === null && count($publishedVariants) === 0) {
            $queryBuilder->withTrashed();
        } else if (count($publishedVariants) === 1) {
            $isPublished = filter_var($publishedVariants[0], FILTER_VALIDATE_BOOLEAN);
            if ($isDeleted) {
                $queryBuilder->where(function($query) use ($isPublished) {
                    $query->where('is_published', $isPublished)->orWhereNotNull('deleted_at');
                })->withTrashed();
            } else {
                $queryBuilder->where('is_published', $isPublished);
            }
        } else if ($isDeleted === true && !count($publishedVariants)) {
            $queryBuilder->onlyTrashed();
        }

        if ($request->filled('practitioner')) {
            $queryBuilder->whereHas('user', function($query) use ($request) {
                $query->whereIn('id', explode(',', $request->get('practitioner')));
            });
        }

        return $queryBuilder;
    }
}
