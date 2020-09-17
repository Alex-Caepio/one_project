<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\RescheduleRequestDeclinedByClient;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class RescheduleRequestDeclinedByClientEmail
{
    public function __construct()
    {
    }

    public function handle(RescheduleRequestDeclinedByClient $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Reschedule Request Declined by Client')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
    }
}
