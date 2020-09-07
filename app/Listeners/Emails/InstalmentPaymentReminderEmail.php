<?php

namespace App\Listeners\Emails;

use App\Events\InstalmentPaymentReminder;

class InstalmentPaymentReminderEmail
{
    public function __construct()
    {
    }

    public function handle(InstalmentPaymentReminder $event): void
    {
    }
}
