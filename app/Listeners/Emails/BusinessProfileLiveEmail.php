<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\BusinessProfileLive;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class BusinessProfileLiveEmail extends SendEmailHandler {

    public function handle(BusinessProfileLive $event): void {
        $this->toEmail = $event->user->email;
        $this->templateName = 'Business Profile Live';
        $this->event = $event;
        $this->sendCustomEmail();
    }
}
