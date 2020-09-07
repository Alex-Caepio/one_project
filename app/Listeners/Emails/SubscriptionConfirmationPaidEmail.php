<?php

namespace App\Listeners\Emails;

use App\Events\SubscriptionConfirmationPaid;

class SubscriptionConfirmationPaidEmail
{
    public function __construct()
    {
    }

    public function handle(SubscriptionConfirmationPaid $event): void
    {
    }
}
