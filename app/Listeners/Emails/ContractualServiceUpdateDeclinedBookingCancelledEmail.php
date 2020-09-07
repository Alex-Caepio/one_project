<?php

namespace App\Listeners\Emails;

use App\Events\ContractualServiceUpdateDeclinedBookingCancelled;

class ContractualServiceUpdateDeclinedBookingCancelledEmail
{
    public function __construct()
    {
    }

    public function handle(ContractualServiceUpdateDeclinedBookingCancelled $event): void
    {
    }
}
