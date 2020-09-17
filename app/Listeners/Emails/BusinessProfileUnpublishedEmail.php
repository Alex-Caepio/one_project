<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\BusinessProfileUnpublished;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class BusinessProfileUnpublishedEmail
{
    public function __construct()
    {
    }

    public function handle(BusinessProfileUnpublished $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Business Profile Unpublished')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
    }
}
