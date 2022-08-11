<?php

namespace App\Events;

use App\Models\Booking;
use App\Models\GoogleCalendar;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentBooked
{
    use Dispatchable, InteractsWithSockets, SerializesModels, EventFillableFromBooking;

    public ?GoogleCalendar $calendar;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Booking $booking
     */
    public function __construct(Booking $booking)
    {
        $this->setBooking($booking);
        $this->calendar = $this->practitioner->calendar;
    }
}
