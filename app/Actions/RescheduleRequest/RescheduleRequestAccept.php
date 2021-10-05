<?php


namespace App\Actions\RescheduleRequest;

use App\Events\BookingRescheduleAcceptedByClient;
use App\Models\RescheduleRequest;

class RescheduleRequestAccept {

    public function execute(RescheduleRequest $rescheduleRequest, bool $informPractitioner = true): void {
        $booking = $rescheduleRequest->booking;
        $booking->schedule_id = $rescheduleRequest->new_schedule_id;
        $booking->datetime_from = $rescheduleRequest->new_start_date;
        $booking->datetime_to = $rescheduleRequest->new_end_date;
        $booking->status = 'rescheduled';

        $booking->update();
        event(new BookingRescheduleAcceptedByClient($booking, $informPractitioner));
        $rescheduleRequest->delete();
    }
}
