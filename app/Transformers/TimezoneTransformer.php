<?php


namespace App\Transformers;

use App\Models\Timezone;

class TimezoneTransformer extends Transformer
{
    public function transform(Timezone $timezone): array
    {
        return [
            'id'           => $timezone->id,
            'value'        => $timezone->value,
            'label'        => $timezone->label,
        ];
    }

}
