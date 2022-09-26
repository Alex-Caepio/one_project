<?php

namespace App\Observers;

use App\Actions\Booking\ScheduleAftermath;
use App\Actions\Schedule\CreateRescheduleRequestsOnScheduleUpdate;
use App\Events\ServiceScheduleCancelled;
use App\Events\ServiceScheduleLive;
use App\Events\ServiceUpdatedByPractitionerNonContractual;
use App\Models\Schedule;
use App\Models\Service;

class ScheduleObserver
{
    public function saved(Schedule $schedule): void
    {
        $requestCancelFlag = request('cancel_bookings', false);

        if (
            $schedule->isDirty('is_published') ||
            (!$schedule->isDirty('is_published') && (bool)$requestCancelFlag === true)
        ) {
            if (!$schedule->is_published && !$schedule->wasRecentlyCreated) {
                if ((bool)$requestCancelFlag === true) {
                    run_action(ScheduleAftermath::class, $schedule);
                }
            } elseif ($schedule->is_published) {
                event(new ServiceScheduleLive($schedule));
            }
        }
    }

    public function deleting(Schedule $schedule)
    {
        if ($schedule->is_published) {
            event(new ServiceScheduleCancelled($schedule));
        }

        run_action(ScheduleAftermath::class, $schedule);
    }

    public function updated(Schedule $schedule)
    {
        $hasContractualChanges = $schedule->hasContractualChanges();

        if (
            in_array($schedule->service->service_type_id, [Service::TYPE_WORKSHOP, Service::TYPE_EVENT, Service::TYPE_RETREAT])
            && $hasContractualChanges
        ) {
            $schedule->resetUpdateStatuses();
            run_action(CreateRescheduleRequestsOnScheduleUpdate::class, $schedule);
        }

        if (
            $schedule->hasNonContractualChanges()
            && !$hasContractualChanges
            && !in_array($schedule->service->service_type->id, [Service::TYPE_BESPOKE])
        ) {
            $schedule->resetUpdateStatuses();
            event(new ServiceUpdatedByPractitionerNonContractual($schedule));
        }
    }
}
