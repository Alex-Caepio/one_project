<?php

namespace App\Listeners\Emails;

use App\Events\PasswordChanged;

class PasswordChangedEmail extends SendEmailHandler {

    public function handle(PasswordChanged $event): void {
        $this->toEmail = $event->user->email;
        $this->templateName = 'Password Changed';
        $this->event = $event;
        $this->sendCustomEmail();

    }
}
