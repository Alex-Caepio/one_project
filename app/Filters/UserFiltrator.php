<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Request;

class UserFiltrator {

    public function apply(Builder $queryBuilder, Request $request) {

        $isClient = $request->getBoolFromRequest('is_client');
        if ($isClient) {
            $queryBuilder->whereHas('bookings', static function($query) {
                $query->where('bookings.practitioner_id', Auth::id());
            });
        }

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

        $searchTerms = null;
        if ($request->filled('search')) {
            $searchTerms = $request->get('search');
        } else if ($request->filled('q')) {
            $searchTerms = $request->get('q');
        }

        if ($searchTerms !== null) {
            $search = '%' . $searchTerms . '%';
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
