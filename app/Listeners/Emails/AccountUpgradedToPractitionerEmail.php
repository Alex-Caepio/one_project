<?php

namespace App\Listeners\Emails;

use App\Events\AccountUpgradedToPractitioner;

class AccountUpgradedToPractitionerEmail
{
    public function __construct()
    {
    }

    public function handle(AccountUpgradedToPractitioner $event): void
    {
    }
}
