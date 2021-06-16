<?php


namespace App\Actions\Booking;

use App\Actions\Cancellation\CancelBooking;
use App\Models\Booking;
use App\Models\RescheduleRequest;
use App\Models\Schedule;

class ScheduleAftermath {

    public function execute(Schedule $schedule) {
        RescheduleRequest::where('schedule_id', $schedule->id)->delete();
        RescheduleRequest::where('new_schedule_id', $schedule->id)->delete();

        $bookings = Booking::where('schedule_id', $schedule->id)->active()->get();
        if (count($bookings)) {
            foreach ($bookings as $booking) {
                run_action(CancelBooking::class, $booking);
            }
        }
    }

}
