<?php

namespace App\Listeners\Emails;

use App\Events\ServiceUpdatedByPractitionerNonContractual;

class ServiceUpdatedByPractitionerNonContractualEmail
{
    public function __construct()
    {
    }

    public function handle(ServiceUpdatedByPractitionerNonContractual $event): void
    {
    }
}
