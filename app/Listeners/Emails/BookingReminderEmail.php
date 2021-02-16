<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\BookingReminder;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class BookingReminderEmail {

    public function handle(BookingReminder $event): void {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', $event->type)->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function($message) use ($user) {
            $message->to($user->email);
        });
    }

}
