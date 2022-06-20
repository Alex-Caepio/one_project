<?php

namespace App\Listeners\Emails;

use App\Events\BookingRescheduleAcceptedByClient;
use App\Models\User;

class BookingRescheduleAcceptedByClientEmail extends SendEmailHandler
{
    public function handle(BookingRescheduleAcceptedByClient $event): void
    {
        $this->templateName = 'Booking Reschedule Accepted by Client';
        $this->event = $event;

        // client
        $this->toEmail = $event->client->email;
        $this->type = User::ACCOUNT_CLIENT;
        $this->event->recipient = $event->client;
        $this->sendCustomEmail();

        if ($event->informPractitioner) {
            //practitioner
            $this->toEmail = $event->practitioner->email;
            $this->type = User::ACCOUNT_PRACTITIONER;
            $this->event->recipient = $event->practitioner;
            $this->sendCustomEmail();
        }
    }
}
