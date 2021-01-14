<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\InstalmentPaymentReminder;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class InstalmentPaymentReminderEmail {

    public function handle(InstalmentPaymentReminder $event): void {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Instalment Payment Reminder')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function($message) use ($user) {
            $message->to($user->email);
        });
    }
}
