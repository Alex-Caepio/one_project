<?php

namespace App\Listeners\Emails;

use App\Events\ServiceScheduleLive;

class ServiceScheduleLiveEmail extends SendEmailHandler {

    public function handle(ServiceScheduleLive $event): void {
        $this->toEmail = $event->user->email;
        $this->templateName = $event->template;
        if ($this->templateName === null) {
            return;
        }
        $this->event = $event;
        $this->sendCustomEmail();
    }
}
