<?php


namespace App\Events;

use App\Models\Booking;
use App\Models\Price;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;

trait EventFillableFromBooking
{

    public User $user;
    public User $client;
    public User $practitioner;
    public Service $service;
    public Booking $booking;
    public Schedule $schedule;
    public ?Price $price;

    public function fillEvent(): void
    {
        if (!$this->booking instanceof Booking) {
            return;
        }
        $this->user = $this->client = $this->booking->user;
        $this->practitioner = $this->booking->practitioner;
        $this->schedule = $this->booking->schedule;
        $this->service = $this->booking->schedule->service;
        $this->price = $this->booking->price;
    }

}
