<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\UserRegistered;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class WelcomeVerification
{
    public function handle(UserRegistered $event): void
    {
        $user = $event->user;

        $emailVerification = CustomEmail::where('name', 'Welcome Verification')
            ->where('user_type', $user->account_type)
            ->first();
        if ($emailVerification) {
            $body = $emailVerification->text;
            $emailVariables = new EmailVariables($event);
            $bodyReplaced = $emailVariables->replace($body);

            Mail::html($bodyReplaced, function($message) use ($user) {
                $message->to($user->email);
            });
        }
    }


}
