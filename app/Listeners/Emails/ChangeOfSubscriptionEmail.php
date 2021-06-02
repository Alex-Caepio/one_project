<?php

namespace App\Listeners\Emails;

use App\Events\ChangeOfSubscription;
use App\Helpers\UserRightsHelper;

class ChangeOfSubscriptionEmail extends SendEmailHandler {

    public function handle(ChangeOfSubscription $event): void {
        $this->sendNotification($event);
        $this->checkDowngradePractitioner($event);
    }


    private function sendNotification(ChangeOfSubscription $event): void {
        $this->toEmail = $event->user->email;
        $this->templateName = 'Change of Subscription';
        $this->event = $event;
        $this->sendCustomEmail();
    }


    private function checkDowngradePractitioner(ChangeOfSubscription $event): void {
        UserRightsHelper::downgradePractitioner($event->user);
    }

}
