<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\RescheduleRequestNoReplyFromClient;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class RescheduleRequestNoReplyFromClientEmail extends SendEmailHandler {
    protected ?string $templateName = 'Reschedule Request No Reply from Client';

    public function handle(RescheduleRequestNoReplyFromClient $event): void {
        $this->event = $event;
        $this->toEmail = $event->practitioner->email;
        $this->type = 'practitioner';
        $this->event->recipient = $event->practitioner;
        $this->sendCustomEmail();
    }
}
