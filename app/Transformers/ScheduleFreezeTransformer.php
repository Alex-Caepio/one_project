<?php


namespace App\Transformers;

use App\Models\ScheduleFreeze;

class ScheduleFreezeTransformer extends Transformer
{
    protected $availableIncludes = [
        'schedule',
        'user',
        'practitioner'
    ];

    public function transform(ScheduleFreeze $freeze): array
    {
        return [
            'id' => $freeze->id,
            'schedule_id' => $freeze->schedule_id,
            'user_id' => $freeze->user_id,
            'practitioner_id' => $freeze->practitioner_id,
            'quantity' => $freeze->quantity,
            'start_at' => $freeze->start_at,
            'freeze_at' => $freeze->freeze_at,
            'created_at' => $freeze->created_at,
            'updated_at' => $freeze->updated_at,
            'price_id' => $freeze->price_id,
            'checkout' => $freeze->checkout_id,
        ];
    }

    public function includeUser(ScheduleFreeze $freeze)
    {
        return $this->itemOrNull($freeze->user, new UserTransformer());
    }

    public function includePractitioner(ScheduleFreeze $freeze)
    {
        return $this->itemOrNull($freeze->practitioner, new UserTransformer());
    }

    public function includeSchedule(ScheduleFreeze $freeze)
    {
        return $this->itemOrNull($freeze->schedule, new ScheduleTransformer());
    }

}
