<?php

namespace App\Listeners\Emails;

use App\Events\ServiceUpdatedNonContractual;
use App\Models\Booking;
use App\Models\Schedule;

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
                Schedule::query()->select('id')->where('schedules.service_id', $this->event->service->id)
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
