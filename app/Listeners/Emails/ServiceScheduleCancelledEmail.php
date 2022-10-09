<?php

namespace App\Listeners\Emails;

use App\Events\ServiceScheduleCancelled;

class ServiceScheduleCancelledEmail extends SendEmailHandler {

    public function handle(ServiceScheduleCancelled $event): void {
        $this->toEmail = $event->user->business_email ?? $event->user->email;
        $this->templateName = 'Service Schedule Cancelled';
        $this->event = $event;
        $this->sendCustomEmail();
    }
}
