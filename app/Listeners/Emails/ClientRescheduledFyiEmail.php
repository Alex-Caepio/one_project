<?php

namespace App\Listeners\Emails;

use App\Events\ClientRescheduledFyi;

class ClientRescheduledFyiEmail
{
    public function __construct()
    {
    }

    public function handle(ClientRescheduledFyi $event): void
    {
    }
}
