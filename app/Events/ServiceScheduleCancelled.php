<?php

namespace App\Events;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServiceScheduleCancelled {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Schedule $schedule;
    public User $user;

    public function __construct(Schedule $schedule, User $user) {
        $this->schedule = $schedule;
        $this->user = $user;
    }
}
