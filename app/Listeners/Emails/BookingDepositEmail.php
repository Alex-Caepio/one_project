<?php

namespace App\Listeners\Emails;

use App\Events\BookingDeposit;
use App\Models\User;

class BookingDepositEmail extends SendEmailHandler
{
    /**
     * @param \App\Events\BookingDeposit $event
     */
    public function handle(BookingDeposit $event): void
    {
        if ($event->template === null) {
            return;
        }
        $this->templateName = $event->template;
        $this->event = $event;

        // client
        $this->toEmail = $event->user->email;
        $this->type = User::ACCOUNT_CLIENT;
        $this->event->recipient = $event->user;
        $this->sendCustomEmail();

        //practitioner
        $this->toEmail = $event->practitioner->email;
        $this->type = User::ACCOUNT_PRACTITIONER;
        $this->event->recipient = $event->practitioner;
        $this->sendCustomEmail();
    }
}
