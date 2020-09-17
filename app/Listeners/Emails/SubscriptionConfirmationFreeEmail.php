<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\SubscriptionConfirmationFree;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class SubscriptionConfirmationFreeEmail
{
    public function __construct()
    {
    }

    public function handle(SubscriptionConfirmationFree $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Subscription confirmation - Free')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
    }
}
