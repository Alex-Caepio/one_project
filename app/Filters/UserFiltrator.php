<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Request;

class UserFiltrator {

    public function apply(Builder $queryBuilder, Request $request) {

        $isAuthor = $request->getBoolFromRequest('is_author');
        if ($isAuthor !== null) {
            if ($isAuthor) {
                $queryBuilder->has('articles');
            } else {
                $queryBuilder->doesntHave('articles');
            }
        }

        $isPublished = $request->getBoolFromRequest('is_published');
        if ($isPublished !== null) {
            $queryBuilder->where('is_published', $isPublished);
        }

        if ($request->filled('search')) {
            $search = '%' . trim($request->get('search')) . '%';
            $emailReplace = str_replace(' ', '+', $search);
            $queryBuilder->where(function($query) use ($search, $emailReplace) {
                $query->where('first_name', 'LIKE', $search)
                      ->orWhere('last_name', 'LIKE', $search)
                      ->orWhere('business_email', 'LIKE', $emailReplace)
                      ->orWhere('email', 'LIKE', $emailReplace)
                      ->orWhere('business_name', 'LIKE', $search);
            });
        }

        $status = $request->getArrayFromRequest('status');
        if (count($status)) {
            $queryBuilder->whereIn('status', $status);
        }

        if ($request->hasOrderBy()) {
            $order = $request->getOrderBy();
            $queryBuilder->orderBy($order['column'], $order['direction']);
        }

    }
}
