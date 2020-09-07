<?php

namespace App\Listeners\Emails;

use App\Events\UserRegistered;

class BookingReminderWsEventEmail
{
    public function __construct()
    {
    }

    public function handle(UserRegistered $event): void
    {
    }
}
