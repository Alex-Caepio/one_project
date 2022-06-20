<?php

namespace App\Listeners\Emails;

use App\Events\AccountTerminatedByAdmin;
use App\Models\User;

class AccountTerminatedByAdminEmail extends SendEmailHandler
{
    public function handle(AccountTerminatedByAdmin $event): void
    {
        $this->type = $event->user->isClient() ? User::ACCOUNT_CLIENT : User::ACCOUNT_PRACTITIONER;
        $this->toEmail = $event->user->email;
        $this->templateName = 'Account Terminated by Admin';
        $this->event = $event;
        $this->sendCustomEmail();
    }
}
