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
    use Dispatchable, InteractsWithSockets, SerializesModels, EventFillableFromBooking;

    public User $recipient;
    public ?string $template;

    /**
     * @param \App\Models\Booking $booking
     */
    public function __construct(Booking $booking) {
        $this->booking = $booking;
        $this->fillEvent();
        $this->template = $this->getTemplate();
    }

    /**
     * @return string
     */
    private function getTemplate(): ?string {
        if ($this->schedule->appointment === 'physical') {
            if ($this->service->service_type_id === 'courses') {
                return 'Booking Confirmation - Dateless Physical';
            }

            return 'Booking Confirmation - Date/Apt Physical';
        }

        if ($this->schedule->appointment === 'virtual') {
            if ($this->service->service_type_id === 'courses') {
                return 'Booking Confirmation - DateLess Virtual';
            }

            return 'Booking Confirmation - Event Virtual';
        }

        return null;
    }
}
