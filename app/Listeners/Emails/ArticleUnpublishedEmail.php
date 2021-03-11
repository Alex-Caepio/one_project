<?php

namespace App\Listeners\Emails;

use App\Events\ArticleUnpublished;

class ArticleUnpublishedEmail extends SendEmailHandler {

    public function handle(ArticleUnpublished $event): void {
        $this->toEmail = $event->user->email;
        $this->templateName = 'Article Unpublished';
        $this->event = $event;
        $this->sendCustomEmail();
    }
}
