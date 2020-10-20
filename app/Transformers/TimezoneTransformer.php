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
            'created_at'   => $this->dateTime($timezone->created_at),
            'updated_at'   => $this->dateTime($timezone->updated_at),
        ];
    }

}
