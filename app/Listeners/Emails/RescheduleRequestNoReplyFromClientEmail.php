<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\RescheduleRequestNoReplyFromClient;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class RescheduleRequestNoReplyFromClientEmail
{
    public function __construct()
    {
    }

    public function handle(RescheduleRequestNoReplyFromClient $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Reschedule Request No Reply from Client')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
    }
}
