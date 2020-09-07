<?php

namespace App\Listeners\Emails;

use App\Events\BusinessProfileLive;

class BusinessProfileLiveEmail
{
    public function __construct()
    {
    }

    public function handle(BusinessProfileLive $event): void
    {
    }
}
