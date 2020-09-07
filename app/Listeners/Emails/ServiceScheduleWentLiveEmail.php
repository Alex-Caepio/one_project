<?php

namespace App\Listeners\Emails;

use App\Events\ServiceScheduleWentLive;

class ServiceScheduleWentLiveEmail
{
    public function __construct()
    {
    }

    public function handle(ServiceScheduleWentLive $event): void
    {
       // Appointments Email
       // Date Less Email
       // Event Virtual Email
       // Retreat Email
       // Ws Event Physical Email
    }
}
