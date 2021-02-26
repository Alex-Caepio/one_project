<?php


namespace App\Events;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;

trait EventFillableFromBooking {

    public User $user;
    public User $practitioner;
    public Service $service;
    public Booking $booking;
    public Schedule $schedule;

    public function fillEvent(Booking $booking): void {
        $this->user = $booking->user;
        $this->practitioner = $booking->practitioner;
        $this->schedule = $booking->schedule;
        $this->service = $booking->schedule->service;
    }

}
