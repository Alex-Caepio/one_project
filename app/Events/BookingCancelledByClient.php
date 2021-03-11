<?php

namespace App\Events;

use App\Models\Booking;
use App\Models\Cancellation;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCancelledByClient {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Booking $booking;
    public Schedule $schedule;
    public Service $service;
    public Cancellation $cancellation;
    public User $user;
    public User $practitioner;
    public User $client;

    public User $recipient;

    public string $template;

    public function __construct(Booking $booking, Cancellation $cancellation, User $practitioner) {
        $this->user = $this->client = Auth::user();
        $this->practitioner = $practitioner;
        $this->booking = $booking;
        $this->schedule = $booking->schedule;
        $this->service = $booking->schedule->service;
        $this->cancellation = $cancellation;

        $this->template = $cancellation->amount >
                          0 ? 'Booking Cancelled by Client with Refund' : 'Booking Cancelled by Client NO Refund';

    }
}
