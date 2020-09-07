<?php

namespace App\Listeners\Emails;

use App\Events\ServiceScheduleCancelled;

class ServiceScheduleCancelledEmail
{
    public function __construct()
    {
    }

    public function handle(ServiceScheduleCancelled $event): void
    {
    }
}
