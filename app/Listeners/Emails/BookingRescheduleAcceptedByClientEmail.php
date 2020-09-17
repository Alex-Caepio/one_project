<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\BookingRescheduleAcceptedByClient;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class BookingRescheduleAcceptedByClientEmail
{
    public function __construct()
    {
    }

    public function handle(BookingRescheduleAcceptedByClient $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Booking Reschedule Accepted by Client')->where('user_type', $user->account_type)->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
    }
}
