<?php


namespace App\Actions\Schedule;


use App\Http\Requests\Request;
use App\Http\Requests\Schedule\CreateScheduleInterface;
use App\Models\Schedule;
use App\Models\Service;

abstract class ScheduleSave {


    protected function saveRelations($request, Schedule $schedule): void {
        if ($request->has('media_files')) {
            $schedule->media_files()->delete();
            $schedule->media_files()->createMany($request->get('media_files'));
        }

        if ($request->filled('schedule_availabilities')) {
            $schedule->schedule_availabilities()->delete();
            $schedule->schedule_availabilities()->createMany($request->get('schedule_availabilities'));
        }

        if ($request->filled('schedule_unavailabilities')) {
            $schedule->schedule_unavailabilities()->delete();
            $schedule->schedule_unavailabilities()->createMany($request->get('schedule_unavailabilities'));
        }

        if ($request->has('schedule_files')) {
            $schedule->schedule_files()->delete();
            $schedule->schedule_files()->createMany($request->get('schedule_files'));
        }

        if ($request->has('schedule_hidden_files')) {
            $schedule->schedule_hidden_files()->delete();
            $schedule->schedule_hidden_files()->createMany($request->get('schedule_hidden_files'));
        }
    }

}
