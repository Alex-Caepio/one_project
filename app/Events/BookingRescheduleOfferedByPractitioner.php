<?php

namespace App\Events;

use App\Models\RescheduleRequest;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingRescheduleOfferedByPractitioner
{
    use Dispatchable, InteractsWithSockets, SerializesModels, EventFillableFromBooking;

    public RescheduleRequest $reschedule;

    public Schedule  $reschedule_schedule;

    public User $recipient;

    public function __construct(RescheduleRequest $reschedule)
    {
        $this->reschedule = $reschedule;
        $this->reschedule->load([
            'booking',
            'user',
            'new_schedule',
        ]);
        $this->setBooking($reschedule->booking);
        $this->recipient = $reschedule->user;
        $this->reschedule_schedule = $this->reschedule->new_schedule;
    }
}
