<?php

namespace App\Http\Controllers;

use App\Filters\ScheduleFreezeFiltrator;
use App\Models\Service;
use App\Models\ScheduleFreeze;
use App\Http\Requests\Request;
use App\Transformers\ScheduleFreezeTransformer;


class ScheduleFreezesController extends Controller {

    public function index(Service $service, Request $request) {

        $query = ScheduleFreeze::query();

        $freezeFilter = new ScheduleFreezeFiltrator();
        $freezeFilter->apply($query, $request);

        $includes = $request->getIncludes();
        $paginator = $query->with($includes)->paginate($request->getLimit());

        return fractal($paginator->getCollection(), new ScheduleFreezeTransformer())
            ->parseIncludes($request->getIncludes())->toArray();
    }
}
