<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\BookingConfirmation;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class BookingConfirmationEmail
{
    public function __construct()
    {
    }

    public function handle(BookingConfirmation $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', '??????')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
        //LIST OF EMAILS HERE
        //Date Apt Physical Email
        //Dateless Physical Email
        //Dateless Physical With Deposit Email
        //Dateless Virtual Email
        //Dateless Virtual With Deposit Email
        //Date Physical With Deposit Email
        //Event Appt Virtual Email
        //Event Virtual Email
        //Event Virtual With Deposit Email
    }
}
