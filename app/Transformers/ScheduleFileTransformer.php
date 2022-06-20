<?php

namespace App\Transformers;

use App\Models\ScheduleFile;

class ScheduleFileTransformer extends Transformer
{
    public function transform(ScheduleFile $scheduleFile)
    {
        return [
            'id'            => $scheduleFile->id,
            'schedule_id'   => $scheduleFile->schedule_id,
            'url'           => $scheduleFile->url,
            'name'          => $scheduleFile->name,
        ];
    }
}
