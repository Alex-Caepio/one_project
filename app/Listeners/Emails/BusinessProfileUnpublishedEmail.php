<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\BusinessProfileUnpublished;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class BusinessProfileUnpublishedEmail {

    public function handle(BusinessProfileUnpublished $event): void {
        $this->toEmail = $event->user->email;
        $this->templateName = 'Business Profile Unpublished';
        $this->event = $event;
        $this->sendCustomEmail();
    }
}
