<?php

namespace App\Transformers;

use App\Models\ScheduleUnavailabilities;

class ScheduleUnavailabilitiesTransformer extends Transformer
{
    public function transform(ScheduleUnavailabilities $scheduleUnavailabilities)
    {
        return [
            'id'            => $scheduleUnavailabilities->id,
            'schedule_id'   => $scheduleUnavailabilities->schedule_id,
            'start_date'    => $scheduleUnavailabilities->start_date,
            'end_date'      => $scheduleUnavailabilities->end_date,
        ];
    }
}
