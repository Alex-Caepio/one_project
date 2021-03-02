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
    public string $template;

    public function __construct(Booking $booking, string $template) {
        $this->booking = $booking;
        $this->schedule = $booking->schedule;
        $this->service = $booking->schedule->service;
        $this->user = $booking->user;
        $this->practitioner = $booking->practitioner;
        $this->template = $template;
    }
}
