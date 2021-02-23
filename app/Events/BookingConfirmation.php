<?php

namespace App\Events;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Models\Schedule;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmation {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public User $practitioner;
    public User $client;
    public Booking $booking;
    public Schedule $schedule;
    public Service $service;
    public User $recipient;

    /**
     * @param User $user
     * @param \App\Models\Booking $booking
     * @param \App\Models\User $practitioner
     */
    public function __construct(User $user, Booking $booking, User $practitioner) {
        $this->user = $this->client = $user;
        $this->booking = $booking;
        $this->schedule = $booking->schedule;
        $this->service = $booking->schedule->service;
        $this->practitioner = $practitioner;
    }
}
