<?php

namespace App\Services;

use App\EmailVariables\EmailVariables;
use App\Events\ServicePurchased;
use App\Models\CustomEmail;
use Illuminate\Mail\Message;
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
            Mail::html($bodyReplaced, function (Message $message) use ($user, $emailVerification) {
                $message->to($user->email);
                $message->subject($emailVerification->subject);
            });
        } catch (Swift_SwiftException $e) {
            // do nothing if mail fails to be sent
        }
    }
}
