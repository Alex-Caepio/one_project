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
    public bool $informPractitioner;

    public function __construct(Booking $booking, bool $informPractitioner = true) {
        $this->booking = $booking;
        $this->informPractitioner = $informPractitioner;
        $this->fillEvent();
    }
}
