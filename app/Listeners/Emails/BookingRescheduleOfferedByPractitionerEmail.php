<?php

namespace App\Listeners\Emails;

use App\Events\BookingRescheduleOfferedByPractitioner;
use App\Models\Service;

class BookingRescheduleOfferedByPractitionerEmail extends SendEmailHandler {

    public function handle(BookingRescheduleOfferedByPractitioner $event): void {
        $this->toEmail = $event->user->email;
        $this->templateName = $event->service->service_type_id === Service::TYPE_APPOINTMENT
            ? 'Booking Reschedule Offered by Practitioner - Appt'
            : 'Booking Reschedule Offered by Practitioner - Date';
        $this->event = $event;
        $this->sendCustomEmail();
    }
}
