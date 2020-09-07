<?php

namespace App\Listeners\Emails;

use App\Events\SubscriptionConfirmationFree;

class SubscriptionConfirmationFreeEmail
{
    public function __construct()
    {
    }

    public function handle(SubscriptionConfirmationFree $event): void
    {
    }
}
