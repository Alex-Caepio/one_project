<?php

namespace App\Events;

use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingRescheduleAcceptedByClient
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $service;
    public $user;

    public function __construct(Service $service,User $user)
    {
        $this->service = $service;
        $this->user = $user;
    }
}
