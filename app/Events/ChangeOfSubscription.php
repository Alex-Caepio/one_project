<?php

namespace App\Events;

use App\Models\Plan;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChangeOfSubscription {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public Plan $plan;
    public ?Plan $previousPlan;

    public function __construct(User $user, Plan $plan, ?Plan $previousPlan) {
        $this->user = $user;
        $this->plan = $plan;
        $this->previousPlan = $previousPlan;
    }
}
