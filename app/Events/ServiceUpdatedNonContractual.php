<?php

namespace App\Events;

use App\Models\Service;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServiceUpdatedNonContractual
{
    use Dispatchable, InteractsWithSockets, SerializesModels, EventFillableFromBooking;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }
}
