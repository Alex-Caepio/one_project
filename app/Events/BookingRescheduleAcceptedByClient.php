<?php

namespace App\Events;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Notification;
use App\Models\RescheduleRequest;

class BookingRescheduleAcceptedByClient
{
    use Dispatchable, InteractsWithSockets, SerializesModels, EventFillableFromBooking;

    public User $recipient;

    public bool $informPractitioner;

    public string $notificationType;

    public RescheduleRequest $reschedule;

    public function __construct(Booking $booking, bool $informPractitioner, string $notificationType)
    {
        $rescheduleRequest = RescheduleRequest::where([
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id
        ])->firstOrFail();

        $this->setBooking($booking);
        $this->informPractitioner = $informPractitioner;
        $this->notificationType = $notificationType;
        $this->reschedule = $rescheduleRequest;

        $notification = new Notification();
        $notification->receiver_id = $booking->practitioner_id;
        $notification->type = $notificationType;
        $notification->client_id = $booking->user_id;
        $notification->practitioner_id = $booking->practitioner_id;
        $notification->booking_id = $booking->id;
        $notification->title = $booking->schedule->service->title . ' ' . $booking->schedule->title;
        $notification->old_address = $rescheduleRequest->old_location_displayed;
        $notification->new_address = $rescheduleRequest->new_location_displayed;
        $notification->old_datetime = $rescheduleRequest->old_start_date;
        $notification->new_datetime = $rescheduleRequest->new_start_date;
        $notification->old_enddate = $rescheduleRequest->old_end_date;
        $notification->new_enddate = $rescheduleRequest->new_end_date;
        $notification->service_id = $booking->schedule->service_id;
        $notification->datetime_from = $booking->datetime_from;
        $notification->datetime_to = $booking->datetime_to;
        $notification->price_id = $booking->price_id;
        $notification->price_payed = $booking->cost;
        $notification->save();
    }
}
