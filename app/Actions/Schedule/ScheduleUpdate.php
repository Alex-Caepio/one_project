<?php


namespace App\Actions\Schedule;


use App\Http\Requests\Request;
use App\Http\Requests\Schedule\CreateScheduleInterface;
use App\Http\Requests\Schedule\GenericUpdateSchedule;
use App\Models\Schedule;
use App\Models\Service;
use Illuminate\Support\Facades\Log;

class ScheduleUpdate extends ScheduleSave {

    public function execute($request, Schedule $schedule): Schedule {
        $data = $request->all();
        $schedule->update($data);
        $this->updatePrices($request, $schedule);
        $this->saveRelations($request, $schedule);

        return $schedule;
    }

    private function updatePrices($request, Schedule $schedule): void {
        if ($request->has('prices')) {
            run_action(HandlePricesUpdate::class, $request->prices, $schedule);
        }
    }


}
