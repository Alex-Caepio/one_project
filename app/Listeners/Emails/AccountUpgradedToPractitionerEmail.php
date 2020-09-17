<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\AccountUpgradedToPractitioner;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class AccountUpgradedToPractitionerEmail
{
    public function __construct()
    {
    }

    public function handle(AccountUpgradedToPractitioner $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Account Upgraded to Practitioner')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
    }
}
