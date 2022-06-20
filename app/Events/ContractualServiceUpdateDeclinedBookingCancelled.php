<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContractualServiceUpdateDeclinedBookingCancelled {
    use Dispatchable, InteractsWithSockets, SerializesModels, EventFillableFromBooking;

    public function __construct(Booking $booking) {
        $this->booking = $booking;
        $this->booking->load(['user', 'practitioner', 'schedule', 'schedule.service']);
        $this->fillEvent();
    }
}
