<?php

namespace App\Listeners\Emails;

use App\Events\BusinessProfileLive;

class BusinessProfileLiveEmail extends SendEmailHandler {

    public function handle(BusinessProfileLive $event): void {
        $this->toEmail = $event->user->business_email ?? $event->user->email;
        $this->templateName = 'Business Profile Live';
        $this->event = $event;
        $this->sendCustomEmail();
    }
}
