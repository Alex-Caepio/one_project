<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\ServiceListingLive;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class ServiceListingLiveEmail
{
    public function __construct()
    {
    }

    public function handle(ServiceListingLive $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Service Listing Live')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
    }
}
