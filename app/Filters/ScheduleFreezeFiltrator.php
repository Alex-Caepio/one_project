<?php

namespace App\Filters;

use App\Http\Requests\Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ScheduleFreezeFiltrator {


    /**
     * @param \Illuminate\Database\Eloquent\Builder $queryBuilder
     * @param \App\Http\Requests\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Builder $queryBuilder, Request $request): Builder {

        if ($request->filled('schedule_id')) {
            $queryBuilder->where('schedule_id', '=', $request->filled('schedule_id'));
        }

        $userId = $request->filled('user_id') ? $request->get('user_id') : Auth::id();
        $queryBuilder->where('user_id', $userId);

//        $queryBuilder->where('freeze_at', '>', Carbon::now()->subMinutes(15));

        return $queryBuilder;
    }
}
