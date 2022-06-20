<?php

namespace App\Events;

use App\Models\RescheduleRequest;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RescheduleRequestNoReplyFromClient {
    use Dispatchable, InteractsWithSockets, SerializesModels, EventFillableFromBooking;

    public RescheduleRequest $rescheduleRequest;
    public User $recipient;

    public function __construct(RescheduleRequest $rescheduleRequest) {
        $this->rescheduleRequest = $rescheduleRequest;
        $this->reschedule = $rescheduleRequest;
        $this->rescheduleRequest->load([
                                           'booking',
                                           'booking.schedule',
                                           'booking.schedule.service',
                                           'user',
                                           'booking.practitioner'
                                       ]);
        $this->booking = $this->rescheduleRequest->booking;
        $this->fillEvent();


    }
}
