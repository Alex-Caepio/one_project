<?php

namespace App\Events;

use App\Models\RescheduleRequest;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RescheduleRequestNoReplyFromClient
{
    use Dispatchable, InteractsWithSockets, SerializesModels, EventFillableFromBooking;

    public RescheduleRequest $rescheduleRequest;

    public User $recipient;

    public function __construct(RescheduleRequest $rescheduleRequest)
    {
        $this->rescheduleRequest = $rescheduleRequest;
        $this->reschedule = $rescheduleRequest;
        $this->rescheduleRequest->load([
            'booking',
            'user',
        ]);
        $this->setBooking($this->rescheduleRequest->booking);
    }
}
