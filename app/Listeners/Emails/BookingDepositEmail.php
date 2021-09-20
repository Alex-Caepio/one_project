<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\BookingConfirmation;
use App\Events\BookingDeposit;
use App\Models\CustomEmail;
use App\Models\Schedule;
use Illuminate\Support\Facades\Mail;

class BookingDepositEmail extends SendEmailHandler {

    /**
     * @param \App\Events\BookingDeposit $event
     */
    public function handle(BookingDeposit $event): void {
        if ($event->template === null) {
            return;
        }
        $this->templateName = $event->template;
        $this->event = $event;

        // client
        $this->toEmail = $event->user->email;
        $this->type = 'client';
        $this->event->recipient = $event->user;
        $this->sendCustomEmail();

        //practitioner
        $this->toEmail = $event->practitioner->email;
        $this->type = 'practitioner';
        $this->event->recipient = $event->practitioner;
        $this->sendCustomEmail();
    }

}