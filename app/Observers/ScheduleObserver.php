<?php

namespace App\Observers;

use App\Actions\Booking\ScheduleAftermath;
use App\Actions\Schedule\CreateRescheduleRequestsOnScheduleUpdate;
use App\Events\ServiceScheduleCancelled;
use App\Events\ServiceScheduleLive;
use App\Events\ServiceUpdatedByPractitionerNonContractual;
use App\Models\Schedule;

class ScheduleObserver
{
    /**
     * Handle the article "updated" event.
     *
     * @param Schedule $schedule
     * @return void
     */
    public function saved(Schedule $schedule): void
    {
        if ($schedule->isDirty('is_published')) {
            if (!$schedule->is_published && !$schedule->wasRecentlyCreated) {
                $requestCancelFlag = request('cancel_bookings', false);
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
            in_array($schedule->service->service_type_id, ['workshop', 'events', 'retreat'])
            && $hasContractualChanges
        ) {
            run_action(CreateRescheduleRequestsOnScheduleUpdate::class, $schedule);
        }
        if ($schedule->hasNonContractualChanges()
            && !$hasContractualChanges
            && !in_array($schedule->service->service_type->id, ['bespoke'])
        ) {
            event(new ServiceUpdatedByPractitionerNonContractual($schedule));
        }
    }
}
