<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\BookingRescheduleOfferedByPractitioner;
use App\Events\BookingRescheduleOfferedByPractitionerDate;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class BookingRescheduleOfferedByPractitionerEmail
{
    public function __construct()
    {
    }

    public function handle(BookingRescheduleOfferedByPractitioner $event): void
    {
        //'Booking Reschedule Offered by Practitioner - Appt'
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Booking Reschedule Offered by Practitioner - Date')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
    }
}
