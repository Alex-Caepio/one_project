<?php

namespace App\Listeners\Emails;

use App\Events\BookingConfirmation;

class BookingConfirmationEmail
{
    public function __construct()
    {
    }

    public function handle(BookingConfirmation $event): void
    {
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
