<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\BookingRescheduleOfferedByPractitionerAppt;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class BookingRescheduleOfferedByPractitionerApptEmail
{
    public function __construct()
    {
    }

    public function handle(BookingRescheduleOfferedByPractitionerAppt $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Booking Reschedule Offered by Practitioner - Appt')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
    }
}
