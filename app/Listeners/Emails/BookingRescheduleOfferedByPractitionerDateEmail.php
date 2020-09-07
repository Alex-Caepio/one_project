<?php

namespace App\Listeners\Emails;

use App\Events\BookingRescheduleOfferedByPractitionerDate;

class BookingRescheduleOfferedByPractitionerDateEmail
{
    public function __construct()
    {
    }

    public function handle(BookingRescheduleOfferedByPractitionerDate $event): void
    {
    }
}
