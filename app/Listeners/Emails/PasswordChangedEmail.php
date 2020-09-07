<?php

namespace App\Listeners\Emails;

use App\Events\PasswordChanged;

class PasswordChangedEmail
{
    public function __construct()
    {
    }

    public function handle(PasswordChanged $event): void
    {
    }
}
