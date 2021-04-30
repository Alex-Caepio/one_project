<?php

namespace App\Events;

use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServiceScheduleCancelled {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Schedule $schedule;
    public Service $service;
    public User $user;

    public function __construct(Schedule $schedule) {
        $this->schedule = $schedule;
        $this->service = $schedule->service;
        $this->user = $schedule->service->user;
    }
}
