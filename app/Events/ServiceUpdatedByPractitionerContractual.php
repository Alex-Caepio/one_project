<?php

namespace App\Events;

use App\Models\RescheduleRequest;
use App\Models\Schedule;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServiceUpdatedByPractitionerContractual
{
    use Dispatchable, InteractsWithSockets, SerializesModels, EventFillableFromBooking;

    public RescheduleRequest $reschedule;

    public function __construct(Schedule $schedule)
    {
        $this->schedule = $schedule;
    }
}
