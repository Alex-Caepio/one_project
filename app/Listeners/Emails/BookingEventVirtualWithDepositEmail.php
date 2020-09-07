<?php

namespace App\Listeners\Emails;

use App\Events\BookingEventVirtualWithDeposit;

class BookingEventVirtualWithDepositEmail
{
    public function __construct()
    {
    }

    public function handle(BookingEventVirtualWithDeposit $event): void
    {
    }
}
