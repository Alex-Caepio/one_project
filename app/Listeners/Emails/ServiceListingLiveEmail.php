<?php

namespace App\Listeners\Emails;

use App\Events\ServiceListingLive;

class ServiceListingLiveEmail extends SendEmailHandler {

    public function handle(ServiceListingLive $event): void {
        $this->toEmail = $event->user->email;
        $this->templateName = 'Service Listing Live';
        $this->event = $event;
        $this->sendCustomEmail();
    }
}
