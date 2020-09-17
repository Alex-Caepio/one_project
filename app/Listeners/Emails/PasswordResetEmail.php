<?php

namespace App\Listeners\Emails;


use App\EmailVariables\EmailVariables;
use App\Events\PasswordReset;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class PasswordResetEmail
{
    public function __construct()
    {
    }

    public function handle(PasswordReset $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Password Reset')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
    }
}
