<?php

namespace App\Listeners\Emails;

use App\Events\RescheduleRequestDeclinedByClient;

class RescheduleRequestDeclinedByClientEmail
{
    public function __construct()
    {
    }

    public function handle(RescheduleRequestDeclinedByClient $event): void
    {
    }
}
