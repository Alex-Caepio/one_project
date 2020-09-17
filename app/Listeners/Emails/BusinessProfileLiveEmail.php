<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\BusinessProfileLive;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class BusinessProfileLiveEmail
{
    public function __construct()
    {
    }

    public function handle(BusinessProfileLive $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Business Profile Live')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
    }
}
