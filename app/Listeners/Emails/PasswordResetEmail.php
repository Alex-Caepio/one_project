<?php

namespace App\Listeners\Emails;

use App\Events\PasswordReset;

class PasswordResetEmail extends SendEmailHandler {

    public function handle(PasswordReset $event): void {
        $this->toEmail = $event->reset->email;
        $this->templateName = 'Password Reset';
        $this->event = $event;
        $this->sendCustomEmail();
    }
}
