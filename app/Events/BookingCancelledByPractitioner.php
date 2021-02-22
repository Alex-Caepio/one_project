<?php

namespace App\Events;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCancelledByPractitioner {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Booking $booking;
    public User $practitioner;

    public Schedule $schedule;
    public Service $service;
    public User $user;

    public function __construct(Booking $booking, User $practitioner) {
        $this->practitioner = $practitioner;
        $this->booking = $booking;

        $this->user = $booking->user;
        $this->schedule = $booking->schedule;
        $this->service = $booking->schedule->service;
    }
}
