<?php

namespace App\Events;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PasswordChanged {
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public User $user;

    public function __construct(User $user) {
        $this->user = $user;
    }
}
