<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServicePurchased
{
    use Dispatchable, InteractsWithSockets, SerializesModels, EventFillableFromBooking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        $this->fillEvent();
    }
}
