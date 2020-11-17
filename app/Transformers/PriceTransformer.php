<?php


namespace App\Transformers;


use App\Models\Price;

class PriceTransformer extends Transformer
{

    public function transform(Price $price)
    {
        return [
            'id'                => $price->id,
            'amount'            => $price->title,
            'schedule_id'       => $price->service_id,
            'name'              => $price->name,
            'is_free'           => $price->is_free,
            'available_till'    => $price->available_till,
            'min_purchase'      => $price->min_purchase,
            'number_available'  => $price->number_available,
            'created_at'        => $price->created_at,
            'updated_at'        => $price->updated_at,
        ];
    }
}
