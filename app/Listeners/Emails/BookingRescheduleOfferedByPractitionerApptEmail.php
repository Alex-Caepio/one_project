<?php

namespace App\Listeners\Emails;

use App\Events\BookingRescheduleOfferedByPractitionerAppt;

class BookingRescheduleOfferedByPractitionerApptEmail
{
    public function __construct()
    {
    }

    public function handle(BookingRescheduleOfferedByPractitionerAppt $event): void
    {
    }
}
