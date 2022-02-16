<?php

namespace App\Actions\RescheduleRequest;

use App\Events\BookingRescheduleAcceptedByClient;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\RescheduleRequest;

class RescheduleRequestAccept
{
    public function execute(RescheduleRequest $rescheduleRequest, bool $informPractitioner = true): void
    {
        /** @var Booking $booking */
        $booking = $rescheduleRequest->booking;
        $booking->schedule_id = $rescheduleRequest->new_schedule_id;
        $booking->datetime_from = $rescheduleRequest->new_start_date;
        $booking->datetime_to = $rescheduleRequest->new_end_date;
        $booking->price_id = $rescheduleRequest->new_price_id;
        $booking->status = Booking::RESCHEDULED_STATUS;
        $booking->update();

        if ($rescheduleRequest->requested_by === RescheduleRequest::REQUESTED_BY_PRACTITIONER) {
            $notificationType = Notification::RESCHEDULED_BY_PRACTITIONER;
        } elseif ($rescheduleRequest->requested_by === RescheduleRequest::REQUESTED_BY_PRACTITIONER_IN_SCHEDULE) {
            $notificationType = Notification::SCHEDULE_CHANGED_BY_PRACTITIONER;
        }

        event(new BookingRescheduleAcceptedByClient($booking, $informPractitioner, $notificationType));

        $rescheduleRequest->delete();
    }
}
