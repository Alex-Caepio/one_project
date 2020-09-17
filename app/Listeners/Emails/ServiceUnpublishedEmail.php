<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\ServiceUnpublished;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class ServiceUnpublishedEmail
{
    public function __construct()
    {
    }

    public function handle(ServiceUnpublished $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Service Unpublished')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
    }
}
