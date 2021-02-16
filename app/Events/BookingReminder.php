<?php

namespace App\Events;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingReminder {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Schedule $schedule;
    public Service $service;
    public User $user;
    public User $practitioner;
    public Booking $booking;
    public string $type;

    public function __construct(Booking $booking, User $user, User $practitioner, Schedule $schedule, Service $service,
                                string $type) {
        $this->schedule = $schedule;
        $this->service = $service;
        $this->booking = $booking;
        $this->user = $user;
        $this->practitioner = $practitioner;
        $this->type = $type;
    }
}
