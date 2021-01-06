<?php

namespace App\Transformers;

use App\Models\ScheduleAvailability;

class ScheduleAvailabilityTransformer extends Transformer
{
    public function transform(ScheduleAvailability $scheduleAvailability)
    {
        return [
            'id'            => $scheduleAvailability->id,
            'schedule_id'   => $scheduleAvailability->schedule_id,
            'days'          => $scheduleAvailability->days,
            'start_time'    => $scheduleAvailability->start_time,
            'end_time'      => $scheduleAvailability->end_time,
        ];
    }
}
