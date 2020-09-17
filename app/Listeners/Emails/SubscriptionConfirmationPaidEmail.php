<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\SubscriptionConfirmationPaid;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class SubscriptionConfirmationPaidEmail
{
    public function __construct()
    {
    }

    public function handle(SubscriptionConfirmationPaid $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Subscription confirmation - Paid')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
    }
}
