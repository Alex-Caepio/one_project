<?php

namespace App\Events;


use App\Models\Service;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServiceListingLive {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Service $service;
    public User $user;

    public function __construct(Service $service) {
        $this->service = $service;
        $this->user = $service->user;
    }
}
