<?php

namespace App\Listeners\Emails;

use App\Events\AccountTerminatedByAdmin;

class AccountTerminatedByAdminEmail
{
    public function __construct()
    {
    }

    public function handle(AccountTerminatedByAdmin $event): void
    {
    }
}
