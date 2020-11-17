<?php

namespace App\Transformers;

use App\Models\ScheduleAvailabilities;

class ScheduleAvailabilitiesTransformer extends Transformer
{
    public function transform(ScheduleAvailabilities $scheduleAvailabilities)
    {
        return [
            'id'            => $scheduleAvailabilities->id,
            'schedule_id'   => $scheduleAvailabilities->schedule_id,
            'days'          => $scheduleAvailabilities->days,
            'start_time'    => $scheduleAvailabilities->start_time,
            'end_time'      => $scheduleAvailabilities->end_time,
        ];
    }
}
