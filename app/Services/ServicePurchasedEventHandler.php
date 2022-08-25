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
    const WITHOUT_PROMO_CODE = 0;

    public function handle(ServicePurchased $event): void
    {
        $booking = $event->booking;

        if ($booking->discount == self::WITHOUT_PROMO_CODE) {
            return;
        }

        $practitioner = $event->practitioner;
        $emailVerification = CustomEmail::query()->find(96);
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        try {
            Mail::html($bodyReplaced, function (Message $message) use ($practitioner, $emailVerification, $emailVariables) {
                $message->to($practitioner->email);
                $message->subject($emailVariables->replace($emailVerification->subject));
                $message->from($emailVerification->from_email, $emailVerification->from_title);
            });
        } catch (Swift_SwiftException $e) {
            // do nothing if mail fails to be sent
        }
    }
}
