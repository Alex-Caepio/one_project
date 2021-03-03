<?php

namespace App\Events;

use App\Models\RescheduleRequest;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClientRescheduledFyi {
    use Dispatchable, InteractsWithSockets, SerializesModels, EventFillableFromBooking;

    public RescheduleRequest $reschedule;
    public Schedule  $reschedule_schedule;
    public User $recipient;

    public function __construct(RescheduleRequest $reschedule) {
        $this->reschedule = $reschedule;
        $this->reschedule->load([
                                    'booking',
                                    'booking.schedule',
                                    'booking.schedule.service',
                                    'user',
                                    'new_schedule',
                                    'practitioner'
                                ]);
        $this->booking = $reschedule->booking;
        $this->fillEvent();
        $this->recipient = $this->booking->practitioner;
    }
}
