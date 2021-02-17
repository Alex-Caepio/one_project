<?php

namespace App\Events;

use App\Models\Plan;
use App\Models\User;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccountUpgradedToPractitioner {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public Plan $plan;

    public function __construct(User $user, Plan $plan) {
        $this->user = $user;
        $this->plan = $plan;
    }
}
