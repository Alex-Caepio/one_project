<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\BookingRescheduleOfferedByPractitioner;
use App\Events\BookingRescheduleOfferedByPractitionerDate;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class BookingRescheduleOfferedByPractitionerEmail extends SendEmailHandler {

    public function handle(BookingRescheduleOfferedByPractitioner $event): void {
        $this->toEmail = $event->user->email;
        $this->templateName = $event->service->service_type_id === 'appointment'
            ? 'Booking Reschedule Offered by Practitioner - Appt'
            : 'Booking Reschedule Offered by Practitioner - Date';
        $this->event = $event;
        $this->sendCustomEmail();
    }
}
