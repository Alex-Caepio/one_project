<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\BookingEventVirtualWithDeposit;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class BookingEventVirtualWithDepositEmail
{
    public function __construct()
    {
    }

    public function handle(BookingEventVirtualWithDeposit $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Booking Confirmation - Event Virtual With Deposit')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
    }
}
