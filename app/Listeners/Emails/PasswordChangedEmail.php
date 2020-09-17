<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\PasswordChanged;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class PasswordChangedEmail
{
    public function __construct()
    {
    }

    public function handle(PasswordChanged $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Password Changed')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
    }
}
