<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\AccountTerminatedByAdmin;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class AccountTerminatedByAdminEmail extends SendEmailHandler {

    public function handle(AccountTerminatedByAdmin $event): void {
        $this->type = $event->user->isClient() ? 'client' : 'practitioner';
        $this->toEmail = $event->user->email;
        $this->templateName = 'Account Terminated by Admin';
        $this->event = $event;
        $this->sendCustomEmail();
    }
}
