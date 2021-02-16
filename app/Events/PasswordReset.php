<?php

namespace App\Events;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\PasswordReset as ResetModel;

class PasswordReset {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ResetModel $reset;
    public $user;

    public function __construct(ResetModel $reset) {
        $this->reset = $reset;
        $this->user = $reset->user;
    }
}
