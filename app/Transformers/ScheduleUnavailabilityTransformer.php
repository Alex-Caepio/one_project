<?php

namespace App\Transformers;

use App\Models\ScheduleUnavailability;

class ScheduleUnavailabilityTransformer extends Transformer
{
    public function transform(ScheduleUnavailability $scheduleUnavailability)
    {
        return [
            'id'            => $scheduleUnavailability->id,
            'schedule_id'   => $scheduleUnavailability->schedule_id,
            'start_date'    => $scheduleUnavailability->start_date,
            'end_date'      => $scheduleUnavailability->end_date,
        ];
    }
}
