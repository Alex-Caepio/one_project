<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\ServiceScheduleLive;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class ServiceScheduleLiveEmail {

    public function handle(ServiceScheduleLive $event): void {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', $event->type)->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function($message) use ($user) {
            $message->to($user->email);
        });
        // Appointments Email
        // Date Less Email
        // Event Virtual Email
        // Retreat Email
        // Ws Event Physical Email
    }
}
