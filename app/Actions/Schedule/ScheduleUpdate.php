<?php


namespace App\Actions\Schedule;


use App\Models\Schedule;

class ScheduleUpdate extends ScheduleSave {

    public function execute($request, Schedule $schedule): Schedule
    {
        $data = $this->collectRequest($request, $schedule->service);
        if ($this->isFilesUpdated($data['schedule_files'], $schedule)) {
            $schedule->isScheduleFilesUpdated = true;
        }
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

    private function isFilesUpdated(array $files, Schedule $schedule): bool
    {
        $newFilesArr = array_map(function($el){
            return $el['url'];
        }, $files);

        $oldFilesArr = $schedule->schedule_files->pluck('url')->toArray();
        if (count(array_diff($newFilesArr, $oldFilesArr)) > 0) {
            return true;
        }
        return false;
    }
}
