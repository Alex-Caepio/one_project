<?php

namespace App\Filters;

use App\Http\Requests\Request;
use App\Models\Country;
use Illuminate\Database\Eloquent\Builder;


class CountryFiltrator {


    /**
     * @param \Illuminate\Database\Eloquent\Builder $queryBuilder
     * @param \App\Http\Requests\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Builder $queryBuilder, Request $request): Builder {

        if ($request->filled('search')) {
            $search = $request->get('search');
            $queryBuilder->where('iso', 'like', "%$search%")->orWhere('name', 'like', "%$search%")
                                ->orWhere('nicename', 'like', "%$search%")->orWhere('phonecode', 'like', "%$search%");
        }
        return $queryBuilder;
    }
}
