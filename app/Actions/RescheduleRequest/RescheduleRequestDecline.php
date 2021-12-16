<?php

namespace App\Actions\RescheduleRequest;

use App\Actions\Cancellation\CancelBooking;
use App\Events\RescheduleRequestDeclinedByClient;
use App\Models\Notification;
use App\Models\RescheduleRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RescheduleRequestDecline
{
    public function execute(RescheduleRequest $rescheduleRequest): void
    {
        event(new RescheduleRequestDeclinedByClient($rescheduleRequest));

        // declined by client of the booking
        if ($rescheduleRequest->requested_by === User::ACCOUNT_PRACTITIONER && $rescheduleRequest->user_id === Auth::id()) {
            if ((int)$rescheduleRequest->schedule_id === (int)$rescheduleRequest->new_schedule_id) {
                run_action(
                    CancelBooking::class,
                    $rescheduleRequest->booking,
                    true
                );
            } else {
                $booking = $rescheduleRequest->booking;
                $notification = new Notification();
                $notification->receiver_id = $booking->practitioner_id;
                $notification->type = 'declined_by_client';
                $notification->client_id = $booking->user_id;
                $notification->practitioner_id = $booking->practitioner_id;
                $notification->booking_id = $booking->id;
                $notification->title = $booking->schedule->service->title . ' ' . $booking->schedule->title;
                $notification->old_address = $rescheduleRequest->old_location_displayed;
                $notification->new_address = $rescheduleRequest->new_location_displayed;
                $notification->old_datetime = $rescheduleRequest->old_start_date;
                $notification->old_enddate = $rescheduleRequest->old_end_date;
                $notification->new_datetime = $rescheduleRequest->new_start_date;
                $notification->new_enddate = $rescheduleRequest->new_end_date;
                $notification->service_id = $booking->schedule->service_id;
                $notification->datetime_from = $booking->datetime_from;
                $notification->datetime_to = $booking->datetime_to;
                $notification->price_id = $booking->price_id;
                $notification->price_payed = $booking->cost;
                $notification->save();
            }
        }
        $rescheduleRequest->delete();
    }
}
