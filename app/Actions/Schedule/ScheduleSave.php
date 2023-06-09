<?php

namespace App\Actions\Schedule;

use App\Events\ServiceUpdatedByPractitionerNonContractual;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\UserUnavailabilities;

abstract class ScheduleSave
{
    protected function saveRelations($request, Schedule $schedule): void
    {
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
            $schedule->updateScheduleFiles($request->get('schedule_files'));

            if (
                $schedule->hasRelatedChanges()
                && !in_array($schedule->service->service_type->id, [Service::TYPE_BESPOKE])
            ) {
                event(new ServiceUpdatedByPractitionerNonContractual($schedule));
            }
        }

        if ($request->has('schedule_hidden_files')) {
            $schedule->schedule_hidden_files()->delete();
            $schedule->schedule_hidden_files()->createMany($request->get('schedule_hidden_files'));
        }
    }

    public function collectRequest($request, Service $service): array
    {
        $data = $request->all();
        $data['service_id'] = $service->id;
        if (isset($data['deposit_accepted']) && $data['deposit_accepted'] === true) {
            if ($service->service_type_id === Service::TYPE_BESPOKE) {
                $data['deposit_final_date'] = null;
            } else {
                $data['deposit_instalment_frequency'] = null;
                $data['deposit_instalments'] = null;
            }
        }
        return $data;
    }

    /**
     * @param \App\Models\Service $service
     * @param array $data
     *
     * @return never|void
     */
    public function validateUserAvailability(Service $service, array $data)
    {
        $unavailabilities = UserUnavailabilities::where('practitioner_id', $service->user->id)
            ->whereBetween('start_date', [$data['start_date'],$data['end_date']])
            ->orwhereBetween('end_date', [$data['start_date'], $data['end_date']])
            ->get()
            ->count();

        if($unavailabilities){
            return abort(422, 'User unavailable');
        }

    }
}
