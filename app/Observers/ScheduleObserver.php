<?php

namespace App\Observers;

use App\Actions\Schedule\CreateRescheduleRequestsOnScheduleUpdate;
use App\Events\ServiceScheduleCancelled;
use App\Events\ServiceScheduleLive;
use App\Events\ServiceUpdatedByPractitionerNonContractual;
use App\Http\Requests\Request;
use App\Models\Schedule;
use Illuminate\Support\Facades\Log;

class ScheduleObserver {

    /**
     * Handle the article "updated" event.
     *
     * @param \App\Models\Schedule $schedule
     * @return void
     */
    public function saved(Schedule $schedule): void {
        if ($schedule->isDirty('is_published')) {
            if (!$schedule->is_published && !$schedule->wasRecentlyCreated) {
                event(new ServiceScheduleCancelled($schedule));
            } elseif ($schedule->is_published) {
                event(new ServiceScheduleLive($schedule));
            }
        }
    }

    public function deleted(Schedule $schedule) {
        if ($schedule->is_published) {
            event(new ServiceScheduleCancelled($schedule));
        }
    }

    public function updated(Schedule $schedule) {
        if ($schedule->hasNonContractualChanges()) {
            event(new ServiceUpdatedByPractitionerNonContractual($schedule));
        }
        if (in_array($schedule->service->service_type_id, ['workshop', 'events', 'retreat', 'appointment'])) {
            run_action(CreateRescheduleRequestsOnScheduleUpdate::class, $schedule);
        }
    }

}
