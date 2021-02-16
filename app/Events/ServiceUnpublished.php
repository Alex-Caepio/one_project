<?php

namespace App\Events;

use App\Models\Service;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServiceUnpublished {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Service $service;
    public User $user;

    public function __construct(Service $service, User $user) {
        $this->service = $service;
        $this->user = $user;
    }
}
