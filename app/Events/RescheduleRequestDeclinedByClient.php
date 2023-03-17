<?php

namespace App\Events;

use App\Models\RescheduleRequest;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RescheduleRequestDeclinedByClient
{
    use Dispatchable, InteractsWithSockets, SerializesModels, EventFillableFromBooking;

    public RescheduleRequest $reschedule;

    public User $recipient;

    public Schedule $reschedule_schedule;

    public function __construct(RescheduleRequest $reschedule)
    {
        $this->reschedule = $reschedule;
        $this->reschedule->load([
            'user',
            'booking',
            'new_schedule',
        ]);
        $this->reschedule_schedule = $this->reschedule->new_schedule;
        $this->setBooking($this->reschedule->booking);
        $this->recipient = $this->practitioner;
    }
}
