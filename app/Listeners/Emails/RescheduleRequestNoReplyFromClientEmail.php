<?php

namespace App\Listeners\Emails;

use App\Events\RescheduleRequestNoReplyFromClient;
use App\Models\User;

class RescheduleRequestNoReplyFromClientEmail extends SendEmailHandler
{
    protected ?string $templateName = 'Reschedule Request No Reply from Client';

    public function handle(RescheduleRequestNoReplyFromClient $event): void
    {
        $this->event = $event;
        $this->toEmail = $event->practitioner->business_email ?? $event->practitioner->email;
        $this->type = User::ACCOUNT_PRACTITIONER;
        $this->event->recipient = $event->practitioner;

        $this->sendCustomEmail();
        $event->rescheduleRequest->noreply_sent = now();
        $event->rescheduleRequest->save();
    }
}
