<?php

namespace App\Http\Controllers\Admin;

use App\Events\ServiceListingLive;
use App\Filters\ServiceFiltrator;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateServiceRequest;
use App\Http\Requests\Services\ServicePublishRequest;
use App\Models\Schedule;
use App\Models\Service;
use App\Transformers\ScheduleTransformer;
use App\Transformers\ServiceTransformer;
use App\Http\Requests\Request;

class ScheduleController extends Controller {

    public function index(Request $request) {
        $scheduleQuery = Schedule::query()->whereHas('service');
        $scheduleQuery->where(function($q) {
            $q->where('schedules.start_date', '>=', now())->orWhereNull('schedules.start_date');
        });

        $includes = $request->getIncludes();

        $paginator = $scheduleQuery->with($includes)->paginate($request->getLimit());
        $services = $paginator->getCollection();

        $fractal = fractal($services, new ScheduleTransformer())->parseIncludes($includes)->toArray();

        return response($fractal)->withPaginationHeaders($paginator);
    }


    public function show(Schedule $schedule, Request $request) {
        return fractal($schedule, new ScheduleTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    public function destroy(Schedule $schedule) {
        $schedule->delete();
        return response(null, 204);
    }

}
