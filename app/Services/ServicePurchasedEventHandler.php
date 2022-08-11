<?php

namespace App\Services;

use App\EmailVariables\EmailVariables;
use App\Events\ServicePurchased;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class ServicePurchasedEventHandler
{
    public function handle(ServicePurchased $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::query()->find(96);
        $emailVerification->
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
    }
}
