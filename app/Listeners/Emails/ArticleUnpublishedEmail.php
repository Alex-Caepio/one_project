<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\ArticleUnpublished;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class ArticleUnpublishedEmail
{
    public function __construct()
    {
    }

    public function handle(ArticleUnpublished $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Article Unpublished')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
    }
}
