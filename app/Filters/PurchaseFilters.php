<?php

namespace App\Filters;

use App\Http\Requests\Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PurchaseFilters {

    /**
     * @param \Illuminate\Database\Eloquent\Builder $queryBuilder
     * @param \App\Http\Requests\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Builder $queryBuilder, Request $request): Builder {

        if ($request->filled('created_from')) {
            $queryBuilder->where('created_at', '>=',
                                 Carbon::parse($request->get('created_from'))->startOfDay());
        }

        if ($request->filled('created_to')) {
            $queryBuilder->where('created_at', '<=',
                                 Carbon::parse($request->get('created_to'))->endOfDay());
        }

        if ($request->filled('reference')) {
            $queryBuilder->where('reference', 'like', "%" . $request->get('reference') . "%");
        }

        if ($request->filled('promocode_id')) {
            $queryBuilder->where('promocode_id', '=', (int)$request->get('promocode_id'));
        }

        if ($request->filled('price_id')) {
            $queryBuilder->where('price_id', '=', (int)$request->get('price_id'));
        }

        if ($request->filled('service_id')) {
            $queryBuilder->where('service_id', '=', (int)$request->get('service_id'));
        }

        if ($request->filled('schedule_id')) {
            $queryBuilder->where('schedule_id', '=', (int)$request->get('schedule_id'));
        }

        if ($request->filled('user_id')) {
            $queryBuilder->where('user_id', '=', (int)$request->get('user_id'));
        }

        if ($request->hasOrderBy()) {
            $order = $request->getOrderBy();
            $queryBuilder->orderBy($order['column'], $order['direction']);
        }

        return $queryBuilder;
    }
}
