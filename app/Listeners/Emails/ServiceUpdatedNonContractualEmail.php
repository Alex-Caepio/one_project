<?php

namespace App\Listeners\Emails;

use App\Events\ServiceUpdatedNonContractual;
use App\Models\Booking;

/**
 * Template Email - TE#99.
 */
class ServiceUpdatedNonContractualEmail extends SendEmailHandler
{
    public function handle(ServiceUpdatedNonContractual $event): void
    {
        $this->templateName = 'Service Updated by Practitioner (Non-Contractual)';
        $this->event = $event;

        /** @var Booking[] $upcomingBookings */
        $upcomingBookings = Booking::query()
            ->whereIn(
                'schedule_id',
                $this->event->service->schedules()->select('id')->get()
            )
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
