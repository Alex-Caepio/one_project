<?php

namespace App\Events;

use App\Models\Booking;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingDeposit
{
    use Dispatchable, InteractsWithSockets, SerializesModels, EventFillableFromBooking;

    public User $recipient;
    public ?string $template;
    public Purchase $purchase;

    /**
     * @param \App\Models\Booking $booking
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        $this->purchase = $booking->purchase;
        $this->fillEvent();
        $this->template = $this->getTemplate();
    }

    /**
     * @return string
     */
    private function getTemplate(): ?string
    {
        if ($this->schedule->appointment === 'physical') {
            return $this->service->isDateLess(
            ) ? 'Booking Confirmation - DateLess Physical With Deposit' : 'Booking Confirmation - Date Physical With Deposit';
        }

        if ($this->schedule->appointment === 'virtual' || !$this->schedule->appointment) {
            return $this->service->isDateLess(
            ) ? 'Booking Confirmation - DateLess Virtual With Deposit' : 'Booking Confirmation - Event Virtual With Deposit';
        }

        return null;
    }
}