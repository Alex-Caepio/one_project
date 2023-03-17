<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\AccountUpgradedToPractitioner;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class AccountUpgradedToPractitionerEmail extends SendEmailHandler {

    public function handle(AccountUpgradedToPractitioner $event): void {

        $this->toEmail = $event->user->email;
        $this->templateName = 'Account Upgraded to Practitioner';
        $this->event = $event;
        $this->sendCustomEmail();
    }
}
