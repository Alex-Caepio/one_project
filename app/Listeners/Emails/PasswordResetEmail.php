<?php

namespace App\Listeners\Emails;


use App\Events\PasswordReset;

class PasswordResetEmail
{
    public function __construct()
    {
    }

    public function handle(PasswordReset $event): void
    {
    }
}
