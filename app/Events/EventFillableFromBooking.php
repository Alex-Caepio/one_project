<?php

namespace App\Events;

use App\Models\Booking;
use App\Models\Price;
use App\Models\Purchase;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;

/**
 * Fills email data from the booking when it exists.
 */
trait EventFillableFromBooking
{
    public ?User $user = null;

    public ?User $client = null;

    public ?User $practitioner = null;

    public Service $service;

    public ?Booking $booking = null;

    public ?Schedule $schedule = null;

    public ?Price $price;

    public ?Purchase $purchase;

    /**
     * Sets the given booking and prepares data for sending email by the booking.
     */
    public function setBooking(Booking $booking): void
    {
        $this->booking = $booking;
        $this->booking->loadMissing($this->getBookingDependencies());
        $this->fillEvent();
    }

    public function fillEvent(): void
    {
        if ($this->booking === null) {
            return;
        }

        $this->user = $this->client = $this->booking->user;
        $this->practitioner = $this->booking->practitioner;
        $this->schedule = $this->booking->schedule;
        $this->service = $this->booking->schedule->service;
        $this->price = $this->booking->price;
        $this->purchase = $this->booking->purchase;
    }

    public function getBookingDependencies(): array
    {
        return [
            'user',
            'practitioner',
            'schedule',
            'schedule.service',
            'price',
        ];
    }
}
