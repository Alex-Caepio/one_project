<?php

namespace App\Listeners\Emails;

use App\Events\ServiceUpdatedByPractitionerNonContractual;
use App\Models\Booking;

class ServiceUpdatedByPractitionerNonContractualEmail extends SendEmailHandler
{

    public function handle(ServiceUpdatedByPractitionerNonContractual $event): void
    {
        $this->templateName = 'Service Updated by Practitioner (Non-Contractual)';
        $this->event = $event;

        $upcomingBookings = Booking::query()
            ->where(
                'schedule_id',
                $this->event->schedule->id
            )
            ->active()
            ->with([
                'user',
                'practitioner',
                'schedule',
                'schedule.service'
            ])
            ->get();

        foreach ($upcomingBookings as $booking) {
            $this->event->booking = $booking;
            $this->event->fillEvent();
            $this->toEmail = $this->event->user->email;
            $this->sendCustomEmail();
        }
    }
}
