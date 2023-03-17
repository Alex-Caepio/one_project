<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\BookingRescheduleClientToSelectAppt;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class BookingRescheduleClientToSelectApptEmail
{
    public function __construct()
    {
    }

    public function handle(BookingRescheduleClientToSelectAppt $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Booking Reschedule Client to Select - Appt')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
    }
}
