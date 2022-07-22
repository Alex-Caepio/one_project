<?php

namespace App\Actions\Cancellation;

use App\Models\Booking;
use App\Models\Notification;
use App\Models\RescheduleRequest;
use App\Models\User;

/**
 * Notifies user about cancellation. Generates data for the forntend side - a message.
 */
class NotifyUser
{
    public function execute(string $role, Booking $booking, array $refundData)
    {
        $notification = new Notification();
        $isAmendment = false;

        $rescheduleRequest = RescheduleRequest::where('booking_id', $booking->id)->first();

        if ($rescheduleRequest) {
            $notification->old_address = $rescheduleRequest->old_location_displayed;
            $notification->new_address = $rescheduleRequest->new_location_displayed;
            $notification->old_datetime = $rescheduleRequest->old_start_date;
            $notification->new_datetime = $rescheduleRequest->new_start_date;

            $isAmendment = $rescheduleRequest->isAmendment();
            $rescheduleRequest->delete();
        }

        $notification->receiver_id = $role === User::ACCOUNT_CLIENT ? $booking->practitioner_id : $booking->user_id;
        $notification->type = $this->getNotificationType($role, $isAmendment);
        $notification->client_id = $booking->user_id;
        $notification->practitioner_id = $booking->practitioner_id;
        $notification->booking_id = $booking->id;
        $notification->title = $booking->schedule->service->title . ' ' . $booking->schedule->title;

        $notification->service_id = $booking->schedule->service_id;
        $notification->datetime_from = $booking->datetime_from;
        $notification->datetime_to = $booking->datetime_to;
        $notification->price_id = $booking->price_id;
        $notification->price_refunded = $refundData['refundTotal'] > 0 && $booking->is_installment
            ? $refundData['installmentRefund']
            : $refundData['refundTotal']
        ;
        $notification->price_payed = $booking->cost;

        $notification->save();
    }

    private function getNotificationType(string $role, bool $isAmendment): string
    {
        if ($role === User::ACCOUNT_PRACTITIONER) {
            return Notification::BOOKING_CANCELED_BY_PRACTITIONER;
        }

        // The role is always client
        return $isAmendment
            ? Notification::AMENDMENT_CANCELED_BY_PRACTITIONER
            : Notification::BOOKING_CANCELED_BY_CLIENT
        ;
    }
}
