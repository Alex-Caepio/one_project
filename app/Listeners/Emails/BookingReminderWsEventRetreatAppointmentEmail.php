<?php

namespace App\Listeners\Emails;

use App\Events\UserRegistered;

class BookingReminderWsEventRetreatAppointmentEmail
{
    public function __construct()
    {
    }

    public function handle(UserRegistered $event): void
    {
    }
}
