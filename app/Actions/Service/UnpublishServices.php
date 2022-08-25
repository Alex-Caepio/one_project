<?php

namespace App\Actions\Service;

use App\Models\Schedule;
use App\Models\ScheduleFreeze;
use App\Models\User;

class UnpublishServices
{
    /**
     * @param string[] $allowedServiceTypes A list of services to except for unpublishing.
     *                                      Otherwise all the types of services will be unpublished.
     */
    public function execute(User $user, array $allowedServiceTypes = []): void
    {
        $query = $user->services();

        if ($allowedServiceTypes) {
            $query->whereNotIn('services.service_type_id', $allowedServiceTypes);
        }

        $serviceIds = $query->pluck('services.id')->toArray();
        Schedule::whereIn('service_id', $serviceIds)->update(['is_published' => false]);
        ScheduleFreeze::whereIn('schedule_id', $serviceIds)->delete();

        $query->update(['is_published' => false]);
    }
}
