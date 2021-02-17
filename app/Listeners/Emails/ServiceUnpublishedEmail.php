<?php

namespace App\Listeners\Emails;

use App\Events\ServiceUnpublished;

class ServiceUnpublishedEmail {

    public function handle(ServiceUnpublished $event): void {
        $this->toEmail = $event->user->email;
        $this->templateName = 'Service Unpublished';
        $this->event = $event;
        $this->sendCustomEmail();
    }
}
