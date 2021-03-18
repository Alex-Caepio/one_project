<?php


namespace App\Transformers;


use App\Models\Price;

class PriceTransformer extends Transformer
{

    public function transform(Price $price)
    {
        return [
            'id'                => $price->id,
            'cost'              => $price->cost,
            'schedule_id'       => $price->schedule_id,
            'name'              => $price->name,
            'is_free'           => (bool) $price->is_free,
            'available_till'    => $price->available_till,
            'duration'          => $this->formatDuration($price->duration),
            'min_purchase'      => $price->min_purchase,
            'number_available'  => $price->number_available,
            'stripe_id'         => $price->stripe_id,
            'created_at'        => $price->created_at,
            'updated_at'        => $price->updated_at,
        ];
    }

    protected function formatDuration($duration = 0)
    {
        $hours = intdiv($duration, 60);
        $minutes = ($duration % 60);

        $padedHours = str_pad($hours, 2, '0', STR_PAD_LEFT);
        $padedMinutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);

        return "$padedHours:$padedMinutes";
    }
}
