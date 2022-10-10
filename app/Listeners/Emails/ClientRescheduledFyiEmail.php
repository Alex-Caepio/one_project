<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\ClientRescheduledFyi;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class ClientRescheduledFyiEmail extends SendEmailHandler {

    public function handle(ClientRescheduledFyi $event): void {
        $this->toEmail = $event->user->business_email ?? $event->user->email;
        $this->templateName = 'Client Rescheduled FYI';
        $this->event = $event;
        $this->sendCustomEmail();
    }

}
