<?php

namespace App\Events;

use App\Models\RescheduleRequest;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClientRescheduledFyi
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
            'new_schedule',
        ]);
        $this->setBooking($reschedule->booking);
        $this->recipient = $this->booking->practitioner;
        $this->reschedule_schedule = $this->reschedule->new_schedule;
    }
}
