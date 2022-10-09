<?php

namespace App\Listeners\Emails;

use App\Events\BookingCancelledByClient;
use App\Models\User;

class BookingCancelledByClientEmail extends SendEmailHandler
{
    public function handle(BookingCancelledByClient $event): void
    {
        $this->templateName = $event->template;
        $this->event = $event;

        // client
        $this->toEmail = $event->user->email;
        $this->type = User::ACCOUNT_CLIENT;
        $this->event->recipient = $event->user;
        $this->sendCustomEmail();


        //practitioner
        $this->toEmail = $event->practitioner->business_email ?? $event->practitioner->email;
        $this->type = User::ACCOUNT_PRACTITIONER;
        $this->event->recipient = $event->practitioner;
        $this->sendCustomEmail();
    }
}
