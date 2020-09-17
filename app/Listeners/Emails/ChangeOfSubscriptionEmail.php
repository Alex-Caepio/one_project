<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\ChangeOfSubscription;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class ChangeOfSubscriptionEmail
{
    public function __construct()
    {
    }

    public function handle(ChangeOfSubscription $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Change of Subscription')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
    }
}
