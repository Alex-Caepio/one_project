<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\UserRegistered;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class WelcomeVerification extends SendEmailHandler {
    /*
     * @param \App\Events\UserRegistered $event
     */
    public function handle(UserRegistered $event): void {
        $this->toEmail = $event->user->email;
        $this->templateName = 'Welcome Verification';
        $this->event = $event;
        $this->sendCustomEmail();
    }


}
