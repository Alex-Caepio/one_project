<?php

namespace App\Events;

use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServiceScheduleWentLive
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $service;
    public $user;
    public $schedule;

    public function __construct(Service $service, User $user, Schedule $schedule)
    {
        $this->schedule = $schedule;
        $this->service = $service;
        $this->user = $user;
    }
}
