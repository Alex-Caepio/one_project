<?php

namespace App\Listeners\Emails;

use App\Events\PurchaseCancelledByPractitioner;

class PurchaseCancelledByPractitionerEmail
{
    public function __construct()
    {
    }

    public function handle(PurchaseCancelledByPractitioner $event): void
    {
    }
}
