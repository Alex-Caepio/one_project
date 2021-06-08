<?php

namespace App\Observers;

use App\Actions\Cancellation\CancelBooking;
use App\Actions\Schedule\CreateRescheduleRequestsOnScheduleUpdate;
use App\Events\ServiceScheduleCancelled;
use App\Events\ServiceScheduleLive;
use App\Events\ServiceUpdatedByPractitionerNonContractual;
use App\Http\Requests\Request;
use App\Models\Booking;
use App\Models\RescheduleRequest;
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

    public function deleting(Schedule $schedule) {
        if ($schedule->is_published) {
            event(new ServiceScheduleCancelled($schedule));
        }

        RescheduleRequest::where('schedule_id', $schedule->id)->delete();
        RescheduleRequest::where('new_schedule_id', $schedule->id)->delete();

        $bookings = Booking::where('schedule_id', $schedule->id)->active()->get();
        if (count($bookings)) {
            foreach ($bookings as $booking) {
                run_action(CancelBooking::class, $booking);
            }
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
