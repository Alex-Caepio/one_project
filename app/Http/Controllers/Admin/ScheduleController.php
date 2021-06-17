<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Schedule\ScheduleStore;
use App\Actions\Schedule\ScheduleUpdate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Schedule\CreateScheduleInterface;
use App\Http\Requests\Schedule\GenericUpdateSchedule;
use App\Models\Schedule;
use App\Models\Service;
use App\Transformers\ScheduleTransformer;
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

    public function update(GenericUpdateSchedule $request, Schedule $schedule) {
        $schedule = run_action(ScheduleUpdate::class, $request, $schedule);
        return fractal($schedule, new ScheduleTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    public function store(CreateScheduleInterface $request, Service $service) {
        $schedule = run_action(ScheduleStore::class, $request, $service);
        return fractal($schedule, new ScheduleTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    public function publish(Request $request, Schedule $schedule) {
        $schedule->is_published = true;
        $schedule->save();
        return response(null, 204);
    }

    public function unpublish(Request $request, Schedule $schedule) {
        $schedule->is_published = false;
        $schedule->save();
        return response(null, 204);
    }

}
