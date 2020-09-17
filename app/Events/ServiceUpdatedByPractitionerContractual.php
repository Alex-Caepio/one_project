<?php

namespace App\Events;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServiceUpdatedByPractitionerContractual
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $schedule;
    public $user;

    public function __construct(Schedule $schedule, User $user)
    {
        $this->schedule = $schedule;
        $this->user = $user;
    }
}
