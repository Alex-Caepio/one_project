<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\UserRegistered;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class WelcomeVerification
{
    public function __construct()
    {
    }

    public function handle(UserRegistered $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Welcome Verification')->first();
        $body = $emailVerification->text;
       // $subject = $emailVerification->subject;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);
        //  $bodyReplaced =$body.$subject;
        Mail::raw($bodyReplaced, function ($message) use ($user) {
            $message->to($user->email);
        });
    }


}
