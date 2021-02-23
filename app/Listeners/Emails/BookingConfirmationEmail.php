<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\BookingConfirmation;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class BookingConfirmationEmail extends SendEmailHandler {

    /**
     * @var string[]
     */
    private static array $eventTemplates = [
        'events'      => 'Booking Confirmation - Event Virtual',
        //'Booking Confirmation - DateLess Virtual',
        'retreat'     => 'Booking Confirmation - Date/Apt Physical',
        'appointment' => 'Booking Confirmation - Date/Apt Physical',
        'workshop'    => 'Booking Confirmation - Date/Apt Physical',
        'courses'     => 'Booking Confirmation - Dateless Physical'
    ];

    /**
     * @param \App\Events\BookingConfirmation $event
     */
    public function handle(BookingConfirmation $event): void {

        $this->templateName = self::$eventTemplates[$event->service->service_type_id];
        $this->event = $event;

        // client
        $this->toEmail = $event->user->email;
        $this->type = 'client';
        $this->event->recipient = $event->user;
        $this->sendCustomEmail();


        //practitioner
        $this->toEmail = $event->practitioner->email;
        $this->type = 'practitioner';
        $this->event->recipient = $event->practitioner;
        $this->sendCustomEmail();
    }
}
