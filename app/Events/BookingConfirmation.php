<?php

namespace App\Events;

use App\Models\User;
use App\Models\Schedule;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmation
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $schedule;

    /**
     * @param User $user
     */
    public function __construct(User $user, Schedule $schedule )
    {
        $this->user = $user;
        $this->schedule = $schedule;
    }
}
