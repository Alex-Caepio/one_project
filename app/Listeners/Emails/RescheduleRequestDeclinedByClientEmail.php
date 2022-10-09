<?php

namespace App\Listeners\Emails;

use App\Events\RescheduleRequestDeclinedByClient;
use App\Models\User;

class RescheduleRequestDeclinedByClientEmail extends SendEmailHandler
{
    protected ?string $templateName = 'Reschedule Request Declined by Client';

    public function handle(RescheduleRequestDeclinedByClient $event): void
    {
        $this->event = $event;
        $this->toEmail = $event->practitioner->business_email ?? $event->practitioner->email;
        $this->type = User::ACCOUNT_PRACTITIONER;
        $this->sendCustomEmail();
    }
}
