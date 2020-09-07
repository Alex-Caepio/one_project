<?php


namespace App\Actions\RescheduleRequest;


use App\Http\Requests\Request;
use App\Models\RescheduleRequest;
use App\Models\Schedule;

class RescheduleRequestStore
{
    public function execute(Schedule $schedule, Request $request)
    {
        $rescheduleRequest = new RescheduleRequest();
        $rescheduleRequest->forceFill(
            [
                'user_id' => $request->get('users'),
                'schedule_id' => $schedule->id,
                'new_schedule_id' => $request->get('schedules')
            ]
        );
        $rescheduleRequest->save();
    }
}
