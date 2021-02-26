<?php

namespace App\Listeners\Emails;

use App\Events\BookingReminder;

class BookingReminderEmail extends SendEmailHandler {

    public function handle(BookingReminder $event): void {
        $this->toEmail = $event->user->email;
        $this->templateName = $event->template;
        $this->event = $event;
        $this->sendCustomEmail();
    }

}
