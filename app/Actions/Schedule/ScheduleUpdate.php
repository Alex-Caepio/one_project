<?php

namespace App\Actions\Schedule;

use App\Models\Schedule;

class ScheduleUpdate extends ScheduleSave
{
    public function execute($request, Schedule $schedule): Schedule
    {
        $data = $this->collectRequest($request, $schedule->service);

        $schedule->update($data);
        $this->updatePrices($request, $schedule);
        $this->saveRelations($request, $schedule);

        return $schedule;
    }

    private function updatePrices($request, Schedule $schedule): void
    {
        if ($request->has('prices')) {
            run_action(HandlePricesUpdate::class, $request->prices, $schedule);
        }
    }
}
