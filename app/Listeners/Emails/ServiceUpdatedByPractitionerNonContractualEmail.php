<?php

namespace App\Listeners\Emails;

use App\Events\ServiceUpdatedByPractitionerNonContractual;
use App\Models\Booking;

/**
 * Template Email - TE#99.
 */
class ServiceUpdatedByPractitionerNonContractualEmail extends SendEmailHandler
{
    public function handle(ServiceUpdatedByPractitionerNonContractual $event): void
    {
        $this->templateName = 'Service Updated by Practitioner (Non-Contractual)';
        $this->event = $event;

        /** @var Booking $upcomingBookings */
        $upcomingBookings = $this->event->schedule->bookings()
            ->active()
            ->with($this->event->getBookingDependencies())
            ->get()
        ;

        foreach ($upcomingBookings as $booking) {
            $this->event->setBooking($booking);
            $this->toEmail = $this->event->user->email;
            $this->sendCustomEmail();
        }
    }
}
