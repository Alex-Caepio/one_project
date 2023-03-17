<?php

namespace App\Actions\Service;

use App\Models\Schedule;
use App\Models\ScheduleFreeze;
use App\Models\User;

class DeleteServices
{
    public function execute(User $user): void
    {
        $query = $user->services();

        $serviceIds = $query->pluck('services.id')->toArray();
        Schedule::whereIn('service_id', $serviceIds)->delete();
        ScheduleFreeze::whereIn('schedule_id', $serviceIds)->delete();

        $query->delete();
    }
}
