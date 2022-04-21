<?php

namespace App\Listeners\Emails;

use App\Events\BookingCancelledToClient;
use App\Models\User;

class BookingCancelledToClientEmail extends SendEmailHandler
{
    public function handle(BookingCancelledToClient $event): void
    {
        $this->templateName = $event->template;
        $this->event = $event;

        // practitioner
        $this->toEmail = $event->practitioner->email;
        $this->type = User::ACCOUNT_PRACTITIONER;
        $this->event->recipient = $event->practitioner;
        $this->sendCustomEmail();
    }
}
