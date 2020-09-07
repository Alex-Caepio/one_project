<?php

namespace App\Listeners\Emails;

use App\Events\BookingRescheduleAcceptedByClient;

class BookingRescheduleAcceptedByClientEmail
{
    public function __construct()
    {
    }

    public function handle(BookingRescheduleAcceptedByClient $event): void
    {
    }
}
