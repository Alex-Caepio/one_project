<?php

namespace App\Listeners\Emails;

use App\Events\ArticleUnpublished;

class ArticleUnpublishedEmail
{
    public function __construct()
    {
    }

    public function handle(ArticleUnpublished $event): void
    {
    }
}
