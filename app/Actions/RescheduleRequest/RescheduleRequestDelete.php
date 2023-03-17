<?php

namespace App\Actions\RescheduleRequest;

use App\Models\RescheduleRequest;

class RescheduleRequestDelete
{
    public function execute(RescheduleRequest $rescheduleRequest): void
    {
        $rescheduleRequest->delete();
    }
}
