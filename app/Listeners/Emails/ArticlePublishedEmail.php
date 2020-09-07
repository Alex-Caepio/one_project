<?php

namespace App\Listeners\Emails;

use App\Events\ArticlePublished;

class ArticlePublishedEmail
{
    public function __construct()
    {
    }

    public function handle(ArticlePublished $event): void
    {
    }
}
