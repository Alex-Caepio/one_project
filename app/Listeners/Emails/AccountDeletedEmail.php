<?php

namespace App\Listeners\Emails;

use App\Events\AccountDeleted;

class AccountDeletedEmail extends SendEmailHandler {

    public function handle(AccountDeleted $event): void {
        $this->toEmail = $event->user->email;
        $this->templateName = 'Account Deleted';
        $this->event = $event;
        $this->type = $event->user->account_type;
        $this->sendCustomEmail();
    }
}
