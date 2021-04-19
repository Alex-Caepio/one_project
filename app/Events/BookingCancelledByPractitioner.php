<?php

namespace App\Events;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCancelledByPractitioner {
    use Dispatchable, InteractsWithSockets, SerializesModels, EventFillableFromBooking;

    public function __construct(Booking $booking) {
        $this->booking = $booking;
        $this->fillEvent();
    }
}
