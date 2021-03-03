<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\BookingRescheduleAcceptedByClient;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class BookingRescheduleAcceptedByClientEmail extends SendEmailHandler {

    public function handle(BookingRescheduleAcceptedByClient $event): void {
        $this->templateName = 'Booking Reschedule Accepted by Client';
        $this->event = $event;

        // client
        $this->toEmail = $event->client->email;
        $this->type = 'client';
        $this->event->recipient = $event->client;
        $this->sendCustomEmail();


        //practitioner
        $this->toEmail = $event->practitioner->email;
        $this->type = 'practitioner';
        $this->event->recipient = $event->practitioner;
        $this->sendCustomEmail();

    }
}
