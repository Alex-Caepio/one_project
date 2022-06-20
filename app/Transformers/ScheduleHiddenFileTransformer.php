<?php

namespace App\Transformers;

use App\Models\ScheduleHiddenFile;

class ScheduleHiddenFileTransformer extends Transformer
{
    public function transform(ScheduleHiddenFile $scheduleHiddenFile)
    {
        return [
            'id'            => $scheduleHiddenFile->id,
            'schedule_id'   => $scheduleHiddenFile->schedule_id,
            'url'           => $scheduleHiddenFile->url,
            'name'          => $scheduleHiddenFile->name,
        ];
    }
}
