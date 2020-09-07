<?php

namespace App\Listeners\Emails;

use App\Events\ServiceListingLive;

class ServiceListingLiveEmail
{
    public function __construct()
    {
    }

    public function handle(ServiceListingLive $event): void
    {
    }
}
