<?php

namespace App\Http\Controllers;

use App\Filters\ScheduleFreezeFiltrator;
use App\Models\ScheduleFreeze;
use App\Http\Requests\Request;
use App\Transformers\ScheduleFreezeTransformer;
use Illuminate\Support\Carbon;


class ScheduleFreezesController extends Controller {

    public function index(Request $request) {

        $time = Carbon::now()->subMinutes(15);
        $query = ScheduleFreeze::query()
            ->where('freeze_at', '>', $time->toDateTimeString());

        $freezeFilter = new ScheduleFreezeFiltrator();
        $freezeFilter->apply($query, $request);

        $includes = $request->getIncludes();
        $paginator = $query->with($includes)->paginate($request->getLimit());

        return fractal($paginator->getCollection(), new ScheduleFreezeTransformer())
            ->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function destroy(ScheduleFreeze $scheduleFreeze)
    {
        $scheduleFreeze->delete();
        return response(null, 204);
    }
}
