<?php

namespace App\Listeners\Emails;

use App\Models\CustomEmail;
use App\EmailVariables\EmailVariables;
use App\Events\BookingCancelledByClient;
use Illuminate\Support\Facades\Mail;

class BookingCancelledByClientEmail
{
    public function handle(BookingCancelledByClient $event): void
    {
        $user = $event->user;

        $emailVerification = CustomEmail::where('name', 'Booking Cancelled by Client with Refund')
            ->where('user_type', $user->account_type)
            ->first();

        $body           = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced   = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user) {
            $message->to($user->email);
        });
        //THERE ARE THREE EMAILS HERE!
        //By client -  No Refund
        //By client - with refund
        //By practitioner
    }
}
