<?php

namespace App\Observers;

use App\Events\ServiceScheduleCancelled;
use App\Events\ServiceScheduleLive;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;

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
                event(new ServiceScheduleCancelled($schedule, Auth::user()));
            } elseif ($schedule->is_published) {
                event(new ServiceScheduleLive($schedule, Auth::user()));
            }
        }
    }

    public function deleted(Schedule $schedule) {
        if ($schedule->is_published) {
            event(new ServiceScheduleCancelled($schedule, Auth::user()));
        }
    }

}
