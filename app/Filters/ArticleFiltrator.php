<?php

namespace App\Filters;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ArticleFiltrator {

    public function apply(Builder $queryBuilder, Request $request) {

        if ($request->filled('search')) {
            $search = $request->get('search');
            $queryBuilder->where('title', 'like', "%$search%");
        }

        if ($request->filled('published_from')) {
            $queryBuilder->where('created_at', '>=', Carbon::parse($request->published_from)->startOfDay());
        }

        if ($request->filled('published_to')) {
            $queryBuilder->where('created_at', '<=', Carbon::parse($request->published_to)->endOfDay());
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
                $query->whereIn('id', explode(',', $request->get('practitioner')));
            });
        }

        return $queryBuilder;
    }
}
