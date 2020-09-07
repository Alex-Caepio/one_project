<?php

namespace App\Listeners\Emails;

use App\Events\ChangeOfSubscription;

class ChangeOfSubscriptionEmail
{
    public function __construct()
    {
    }

    public function handle(ChangeOfSubscription $event): void
    {
    }
}
