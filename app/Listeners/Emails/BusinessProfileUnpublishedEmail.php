<?php

namespace App\Listeners\Emails;

use App\Events\BusinessProfileUnpublished;

class BusinessProfileUnpublishedEmail extends SendEmailHandler {

    public function handle(BusinessProfileUnpublished $event): void {
        $this->toEmail = $event->user->email;
        $this->templateName = 'Business Profile Unpublished';
        $this->event = $event;
        $this->sendCustomEmail();
    }
}
