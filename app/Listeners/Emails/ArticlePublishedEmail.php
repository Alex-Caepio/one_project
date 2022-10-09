<?php

namespace App\Listeners\Emails;

use App\Events\ArticlePublished;

class ArticlePublishedEmail extends SendEmailHandler {

    public function handle(ArticlePublished $event): void {
        $this->toEmail = $event->user->business_email ?? $event->user->email;
        $this->templateName = 'Article Published';
        $this->event = $event;
        $this->sendCustomEmail();
    }
}
