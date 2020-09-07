<?php


namespace App\Transformers;


use App\Models\Price;

class PriceTransformer extends Transformer
{

    public function transform(Price $price)
    {
        return [
            'id' => $price->id,
            'amount' => $price->title,
            'schedule_id' => $price->service_id,
            'created_at' => $price->created_at,
            'updated_at' => $price->updated_at,
        ];
    }
}
