<?php


namespace App\Actions\Schedule;


use App\Http\Requests\Request;
use App\Http\Requests\Schedule\CreateScheduleInterface;
use App\Http\Requests\Schedule\GenericUpdateSchedule;
use App\Models\Schedule;
use App\Models\Service;

class ScheduleUpdate extends ScheduleSave {

    public function execute(GenericUpdateSchedule $request, Schedule $schedule): Schedule {
        $schedule->update($request->all());
        $this->updatePrices($request, $schedule);
        $this->saveRelations($request, $schedule);
        run_action(CreateRescheduleRequestsOnScheduleUpdate::class, $request, $schedule);
        return $schedule;
    }

    private function updatePrices(GenericUpdateSchedule $request, Schedule $schedule): void {
        if ($request->has('prices')) {
            run_action(HandlePricesUpdate::class, $request->prices, $schedule);
        }
    }


}
