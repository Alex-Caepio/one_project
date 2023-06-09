<?php

namespace App\Events;

use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServiceScheduleLive
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Service $service;
    public User $user;
    public Schedule $schedule;
    public ?string $template;

    public function __construct(Schedule $schedule)
    {
        $this->schedule = $schedule;
        $this->service = $schedule->service;
        $this->user = $schedule->service->user;
        $this->template = $this->getTemplate($schedule);
    }

    private function getTemplate(Schedule $schedule): ?string
    {
        if ($schedule->service->service_type_id === Service::TYPE_RETREAT) {
            return 'Service Schedule Live - Retreat';
        }

        if ($schedule->service->service_type_id === Service::TYPE_APPOINTMENT) {
            return 'Service Schedule Live - Appointments';
        }

        if ($schedule->service->service_type_id === Service::TYPE_BESPOKE) {
            return 'Service Schedule Live - Date-less';
        }

        if ($schedule->appointment === 'physical') {
            return 'Service Schedule Live - WS/Event/Physical';
        }

        if ($schedule->appointment === 'virtual') {
            return 'Service Schedule Live - Event/Virtual';
        }

        return null;
    }
}
