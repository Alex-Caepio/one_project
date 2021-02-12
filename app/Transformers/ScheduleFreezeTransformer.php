<?php


namespace App\Transformers;

use App\Models\ScheduleFreeze;

class ScheduleFreezeTransformer extends Transformer
{
    protected $availableIncludes = [
        'schedule',
        'user'
    ];

    public function transform(ScheduleFreeze $freeze)
    {
        return [
            'id'          => $freeze->id,
            'schedule_id' => $freeze->schedule_id,
            'user_id'     => $freeze->user_id,
            'quantity'    => $freeze->quantity,
            'freeze_at'   => $freeze->freeze_at,
            'created_at'  => $freeze->created_at,
            'updated_at'  => $freeze->updated_at,
        ];
    }

    public function includeUser(ScheduleFreeze $freeze)
    {
        return $this->item($freeze->user, new UserTransformer());
    }

    public function includeSchedule(ScheduleFreeze $freeze)
    {
        return $this->item($freeze->schedule, new ScheduleTransformer());
    }

}
