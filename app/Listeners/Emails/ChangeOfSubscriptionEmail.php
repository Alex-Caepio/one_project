<?php

namespace App\Listeners\Emails;

use App\Events\ChangeOfSubscription;

class ChangeOfSubscriptionEmail extends SendEmailHandler {

    public function handle(ChangeOfSubscription $event): void {
        $this->toEmail = $event->user->email;
        $this->templateName = 'Change of Subscription';
        $this->event = $event;
        $this->sendCustomEmail();
    }
}
