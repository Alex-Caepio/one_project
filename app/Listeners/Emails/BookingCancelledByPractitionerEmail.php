<?php

namespace App\Listeners\Emails;

use App\Events\BookingCancelledByPractitioner;

class BookingCancelledByPractitionerEmail extends SendEmailHandler
{
    public function handle(BookingCancelledByPractitioner $event): void
    {
        $this->toEmail = $event->user->email;
        $this->templateName = 'Booking Cancelled by Practitioner';
        $this->event = $event;
        $this->sendCustomEmail();
    }
}
