<?php

namespace App\Listeners\Emails;

use App\Events\BusinessProfileUnpublished;

class BusinessProfileUnpublishedEmail
{
    public function __construct()
    {
    }

    public function handle(BusinessProfileUnpublished $event): void
    {
    }
}
