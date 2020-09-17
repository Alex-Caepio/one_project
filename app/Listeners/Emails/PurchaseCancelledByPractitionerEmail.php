<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\PurchaseCancelledByPractitioner;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class PurchaseCancelledByPractitionerEmail
{
    public function __construct()
    {
    }

    public function handle(PurchaseCancelledByPractitioner $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Purchase Cancelled by Practitioner')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
    }
}
