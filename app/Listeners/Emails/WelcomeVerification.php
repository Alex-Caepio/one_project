<?php

namespace App\Listeners\Emails;

use App\Events\UserRegistered;


class WelcomeVerification extends SendEmailHandler {
    /*
     * @param \App\Events\UserRegistered $event
     */
    public function handle(UserRegistered $event): void {
        $this->toEmail = $event->user->email;
        $this->templateName = 'Welcome Verification';
        $this->type = $event->user->account_type;
        $this->event = $event;
        $this->sendCustomEmail();
    }


}
