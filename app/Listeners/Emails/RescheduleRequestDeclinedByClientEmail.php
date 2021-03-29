<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\RescheduleRequestDeclinedByClient;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class RescheduleRequestDeclinedByClientEmail extends SendEmailHandler {
    protected ?string $templateName = 'Reschedule Request Declined by Client';

    public function handle(RescheduleRequestDeclinedByClient $event): void {
        $this->toEmail = $event->practitioner->email;
        $this->type = 'practitioner';
        $this->event->recipient = $event->practitioner;
        $this->sendCustomEmail();

    }
}
