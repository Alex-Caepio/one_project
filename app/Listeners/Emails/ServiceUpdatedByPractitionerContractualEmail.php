<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\ServiceUpdatedByPractitionerContractual;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class ServiceUpdatedByPractitionerContractualEmail
{
    public function __construct()
    {
    }

    public function handle(ServiceUpdatedByPractitionerContractual $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Service Updated by Practitioner (Contractual)')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
    }
}
