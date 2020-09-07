<?php

namespace App\Listeners\Emails;

use App\Events\ServiceUpdatedByPractitionerContractual;

class ServiceUpdatedByPractitionerContractualEmail
{
    public function __construct()
    {
    }

    public function handle(ServiceUpdatedByPractitionerContractual $event): void
    {
    }
}
