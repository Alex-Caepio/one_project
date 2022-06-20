<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Request;

class UserFiltrator
{

    public function apply(Builder $queryBuilder, Request $request, bool $excludeNameFields = false)
    {
        $isClient = $request->getBoolFromRequest('is_client');
        if ($isClient) {
            $queryBuilder->whereHas(
                'bookings',
                static function ($query) {
                    $query->where('bookings.practitioner_id', Auth::id());
                }
            );
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
            $queryBuilder->where('users.is_published', $isPublished);
        }

        /* Disciplines */
        if ($request->filled('discipline_id') || $request->filled('disciplineId')) {
            $disciplineId = $request->filled('discipline_id') ? $request->get('discipline_id') : $request->get('disciplineId');
            $queryBuilder->whereHas('disciplines', function ($q) use ($disciplineId) {
                $q->where('disciplines.id', (int)$disciplineId);
            });
        }

        $searchTerms = null;
        if ($request->filled('search')) {
            $searchTerms = $request->get('search');
        } else {
            if ($request->filled('q')) {
                $searchTerms = $request->get('q');
            }
        }

        if ($searchTerms !== null) {
            $search = '%' . $searchTerms . '%';
            $emailReplace = str_replace(' ', '+', $search);
            $queryBuilder->where(
                function ($query) use ($search, $emailReplace, $excludeNameFields) {
                    $query->where(
                        'business_email',
                        'LIKE',
                        $emailReplace
                    )->orWhere('email', 'LIKE', $emailReplace)
                        ->orWhere('business_name', 'LIKE', $search)
                        ->orWhere('business_city', 'LIKE', $search)
                        ->orWhereHas(
                            'keywords',
                            static function ($dQuery) use ($search) {
                                $dQuery->where('keywords.title', 'LIKE', $search);
                            }
                        )->orWhereHas(
                            'disciplines',
                            function ($dQuery) use ($search) {
                                $dQuery->where('name', 'LIKE', $search);
                            }
                        );
                    if (!$excludeNameFields) {
                        $query->orWhere('first_name', 'LIKE', $search)->orWhere('last_name', 'LIKE', $search);
                    }
                }
            );
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
