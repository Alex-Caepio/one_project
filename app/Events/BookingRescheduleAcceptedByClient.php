<?php

namespace App\Events;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingRescheduleAcceptedByClient {
    use Dispatchable, InteractsWithSockets, SerializesModels, EventFillableFromBooking;

    public User $recipient;

    public function __construct(Booking $booking) {
        $this->booking = $booking;
        $this->fillEvent();
    }
}
