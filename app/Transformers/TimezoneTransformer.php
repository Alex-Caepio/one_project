<?php


namespace App\Transformers;

use App\Models\Timezone;

class TimezoneTransformer extends Transformer
{
    public function transform(Timezone $timezone)
    {
        return [
            'id'           => $timezone->id,
            'value'        => $timezone->value,
            'label'        => $timezone->label,
            'created_at'   => $timezone->created_at,
            'updated_at'   => $timezone->updated_at,
        ];
    }

}
