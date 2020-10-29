<?php

namespace App\Filters;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ArticleFiltrator {

    public function apply(Builder $queryBuilder, Request $request) {

        if ($request->filled('published_from')) {

            $queryBuilder->where('created_at', '>=', $request->start_date);
        }

        if ($request->filled('published_to')) {
            $queryBuilder->where('created_at', '<=', $request->end_date);
        }

        if ($request->filled('is_deleted')) {
            if ((bool)$request->get('is_deleted')) {
                $queryBuilder->onlyTrashed();
            }
        } else {
            $queryBuilder->withTrashed();
        }

        if ($request->filled('is_published')) {
            $queryBuilder->where('is_published', (bool)$request->get('is_published'));
        }

        if ($request->filled('practitioner')) {
            $queryBuilder->whereHas('user', function($query) use ($request) {
                $query->where('id', '=', (int)$request->get('practitioner'));
            });
        }

        return $queryBuilder;
    }
}
