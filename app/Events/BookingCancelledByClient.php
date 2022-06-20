<?php

namespace App\Events;

use App\Models\Booking;
use App\Models\Cancellation;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCancelledByClient
{
    use Dispatchable, InteractsWithSockets, SerializesModels, EventFillableFromBooking;

    public Cancellation $cancellation;
    public User $recipient;

    public string $template;

    public function __construct(Booking $booking, Cancellation $cancellation)
    {
        $this->booking = $booking;
        $this->fillEvent();
        $this->cancellation = $cancellation;

        $this->template = $cancellation->amount >
            0 ? 'Booking Cancelled by Client with Refund' : 'Booking Cancelled by Client NO Refund';
    }
}
