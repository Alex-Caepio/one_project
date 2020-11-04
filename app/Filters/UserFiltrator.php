<?php
namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserFiltrator {

    public function apply(Builder $queryBuilder, Request $request) {

        if ($request->filled('is_author')) {
            $isAuthor = filter_var($request->get('is_author'), FILTER_VALIDATE_BOOLEAN);
            if ($isAuthor) {
                $queryBuilder->has('articles');
            } else {
                $queryBuilder->doesntHave('articles');
            }
        }

        if ($request->filled('is_published')) {
            $isPublished = filter_var($request->get('is_published'), FILTER_VALIDATE_BOOLEAN);
            if  ($isPublished) {
                $queryBuilder->published();
            } else {
                $queryBuilder->unpublished();
            }
        }

        return $queryBuilder;
    }
}
