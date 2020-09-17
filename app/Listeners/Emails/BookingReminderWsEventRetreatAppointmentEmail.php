<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\UserRegistered;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class BookingReminderWsEventRetreatAppointmentEmail
{
    public function __construct()
    {
    }

    public function handle(UserRegistered $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Booking Reminder - WS/Event Retreat/Appointment')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
    }
}
