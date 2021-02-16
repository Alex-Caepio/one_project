<?php

namespace App\Events;

use App\Models\Plan;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionConfirmation {

    public const DEFAULT_TEMPLATE = 'free';

    private static array $types = [
        'paid' => 'Subscription confirmation - Paid',
        'free' => 'Subscription confirmation - Free',
    ];

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public Plan $plan;
    public string $template;

    public function __construct(User $user, Plan $plan) {
        $this->user = $user;
        $this->plan = $plan;
        $type = $this->plan->is_free ? 'free' : 'paid';
        $this->template = self::$types[$type] ?? self::$types[self::DEFAULT_TEMPLATE];
    }
}
