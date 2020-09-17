<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\ClientRescheduledFyi;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class ClientRescheduledFyiEmail
{
    public function __construct()
    {
    }

    public function handle(ClientRescheduledFyi $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Client Rescheduled FYI')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
    }
}
