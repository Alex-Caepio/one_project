<?php


namespace App\Actions\Schedule;


use App\Http\Requests\Request;
use App\Models\Schedule;
use App\Models\Service;

class ScheduleStore
{
public function execute(Request $request, Service $service){
    $schedule = new Schedule();
    $schedule->forceFill([
        'title' => $request->get('title'),
        'service_id' => $service->id,
        'start_date' => $request->get('start_date'),
        'end_date' => $request->get('end_date'),
        'cost' => $request->get('cost'),
    ]);
    $schedule->save();
}
}
