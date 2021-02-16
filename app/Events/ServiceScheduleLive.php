<?php

namespace App\Events;

use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServiceScheduleLive {

    public const DEFAULT_EMAIL_TYPE = 'Service Schedule Live - Appointments';

    private static array $typesMatching = [
        'workshop'    => 'Service Schedule Live - WS/Event/Physical',
        'appointment' => 'Service Schedule Live - Appointments',
        'retreat'     => 'Service Schedule Live - Retreat',
        'events'      => 'Service Schedule Live - WS/Event/Physical',
        'courses'     => 'Service Schedule Live - Date-less',
    ];

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Service $service;
    public User $user;
    public Schedule $schedule;
    public string $type;

    public function __construct(Service $service, User $user, Schedule $schedule) {
        $this->schedule = $schedule;
        $this->service = $service;
        $this->user = $user;
        $this->type = self::$typesMatching[$service->service_type_id] ?? self::$typesMatching[self::DEFAULT_EMAIL_TYPE];
    }
}
