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

        $onlyTrashed = true;
        if ($request->filled('is_published')) {
            $onlyTrashed = false;
            $publishedVariants = explode(',', $request->get('is_published'));
            if (count($publishedVariants) === 1) {
                $isPublished = filter_var($request->get('is_published'), FILTER_VALIDATE_BOOLEAN);
                if ($isPublished) {
                    $queryBuilder->published();
                } else {
                    $queryBuilder->unpublished();
                }
            }
        }

        if ($request->filled('is_deleted')) {
            $isDeleted = filter_var($request->get('is_deleted'), FILTER_VALIDATE_BOOLEAN);
            if ($isDeleted && $onlyTrashed) {
                $queryBuilder->onlyTrashed();
            } elseif ($isDeleted && !$onlyTrashed) {
                $queryBuilder->withTrashed();
            }
        }


        if ($request->filled('practitioner')) {
            $queryBuilder->whereHas('user', function($query) use ($request) {
                $query->whereIn('id', explode(',', $request->get('practitioner')));
            });
        }

        return $queryBuilder;
    }
}
