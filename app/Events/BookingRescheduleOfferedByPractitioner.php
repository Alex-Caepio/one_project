<?php

namespace App\Events;

use App\Models\Booking;
use App\Models\RescheduleRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingRescheduleOfferedByPractitioner {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Booking $booking;
    public RescheduleRequest $reschedule;

    public function __construct(RescheduleRequest $reschedule) {
        $this->reschedule = $reschedule;
    }
}
