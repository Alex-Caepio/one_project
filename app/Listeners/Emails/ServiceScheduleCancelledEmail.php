<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\ServiceScheduleCancelled;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class ServiceScheduleCancelledEmail extends SendEmailHandler {

    public function handle(ServiceScheduleCancelled $event): void {
        $this->toEmail = $event->user->email;
        $this->templateName = 'Service Schedule Cancelled';
        $this->event = $event;
        $this->sendCustomEmail();
    }
}
