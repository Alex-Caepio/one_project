<?php

namespace App\Listeners\Emails;

use App\Events\RescheduleRequestNoReplyFromClient;

class RescheduleRequestNoReplyFromClientEmail
{
    public function __construct()
    {
    }

    public function handle(RescheduleRequestNoReplyFromClient $event): void
    {
    }
}
