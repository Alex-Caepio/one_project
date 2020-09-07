<?php

namespace App\Listeners\Emails;

use App\Events\BookingCancelledByClient;

class BookingCancelledByClientEmail
{
    public function __construct()
    {
    }

    public function handle(BookingCancelledByClient $event): void
    {
        //THERE ARE THREE EMAILS HERE!
        //By client -  No Refund
        //By client - with refund
        //By practitioner
    }
}
