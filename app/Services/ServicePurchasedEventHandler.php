<?php

namespace App\Services;

use App\EmailVariables\EmailVariables;
use App\Events\ServicePurchased;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;
use Swift_SwiftException;

class ServicePurchasedEventHandler
{
    public function handle(ServicePurchased $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::query()->find(96);
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        try {
            Mail::raw($bodyReplaced, function ($message) use ($user) {
                $message->to($user->email);
            });
        } catch (Swift_SwiftException $e) {
            // do nothing if mail fails to be sent
        }
    }
}
