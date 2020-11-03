<?php
namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserFiltrator {

    public function apply(Builder $queryBuilder, Request $request) {

        if ($request->filled('is_author')) {
            $queryBuilder->has('articles');
        }

        if ($request->filled('is_published')) {
            $queryBuilder->published();
        }

        return $queryBuilder;
    }
}
