<?php

namespace App\Listeners\Emails;

use App\Events\BookingRescheduleClientToSelectAppt;

class BookingRescheduleClientToSelectApptEmail
{
    public function __construct()
    {
    }

    public function handle(BookingRescheduleClientToSelectAppt $event): void
    {
    }
}
