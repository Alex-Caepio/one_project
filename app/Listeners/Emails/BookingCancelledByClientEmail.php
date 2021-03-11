<?php

namespace App\Listeners\Emails;

use App\Events\BookingCancelledByClient;

class BookingCancelledByClientEmail extends SendEmailHandler {
    public function handle(BookingCancelledByClient $event): void {
        $this->templateName = $event->template;
        $this->event = $event;

        // client
        $this->toEmail = $event->user->email;
        $this->type = 'client';
        $this->event->recipient = $event->user;
        $this->sendCustomEmail();


        //practitioner
        $this->toEmail = $event->practitioner->email;
        $this->type = 'practitioner';
        $this->event->recipient = $event->practitioner;
        $this->sendCustomEmail();
    }
}
