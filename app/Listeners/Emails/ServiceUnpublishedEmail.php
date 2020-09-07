<?php

namespace App\Listeners\Emails;

use App\Events\ServiceUnpublished;

class ServiceUnpublishedEmail
{
    public function __construct()
    {
    }

    public function handle(ServiceUnpublished $event): void
    {
    }
}
