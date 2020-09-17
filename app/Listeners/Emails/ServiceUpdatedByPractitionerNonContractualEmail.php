<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\ServiceUpdatedByPractitionerNonContractual;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class ServiceUpdatedByPractitionerNonContractualEmail
{
    public function __construct()
    {
    }

    public function handle(ServiceUpdatedByPractitionerNonContractual $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Service Updated by Practitioner (Non-Contractual)')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
    }
}
