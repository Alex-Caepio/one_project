<?php

namespace App\Listeners\Emails;

use App\Events\SubscriptionConfirmation;
use App\Helpers\UserRightsHelper;

class SubscriptionConfirmationEmail extends SendEmailHandler {

    public function handle(SubscriptionConfirmation $event): void {
        $this->toEmail = $event->user->email;
        $this->templateName = $event->template;
        $this->event = $event;
        $this->sendCustomEmail();
    }
}
