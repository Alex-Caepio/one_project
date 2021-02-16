<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\ArticlePublished;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class ArticlePublishedEmail {

    public function handle(ArticlePublished $event): void {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Article Published')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function($message) use ($user) {
            $message->to($user->email);
        });
    }
}
