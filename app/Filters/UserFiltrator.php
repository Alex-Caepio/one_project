<?php
namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Request;

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

        if ($request->filled('search')) {
            $search = '%' . $request->get('search') . '%';
            $queryBuilder->where(function($query) use ($search) {
                $query->where('first_name', 'LIKE', $search)->orWhere('last_name', 'LIKE', $search)->orWhere('business_name', 'LIKE', $search);
            });
        }

        if ($request->hasOrderBy()) {
            $order = $request->getOrderBy();
            $queryBuilder->orderBy($order['column'], $order['direction']);
        }

    }
}
