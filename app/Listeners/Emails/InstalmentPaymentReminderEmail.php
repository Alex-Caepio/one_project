<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\InstalmentPaymentReminder;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class InstalmentPaymentReminderEmail extends SendEmailHandler
{

    protected ?string $templateName = 'Instalment Payment Reminder';

    public function handle(InstalmentPaymentReminder $event): void
    {
        $this->event = $event;
        $this->toEmail = $event->client->email;
        $this->event->recipient = $event->client;
        $this->sendCustomEmail();
    }
}
