<?php

namespace App\Transformers;

use App\Actions\Promo\CalculatePromoPrice;

class PromocodeCalculateTransformer extends Transformer {
    public function transform(object $data) {
        $promo = $data->promocode;
        $scheduleCost = $data->scheduleCost;
        return [
            'discount_type'  => $promo->promotion->discount_type,
            'discount_value' => $promo->promotion->discount_value,
            'original_price' => $scheduleCost,
            'total_price'    => run_action(CalculatePromoPrice::class, $promo, $scheduleCost)
        ];
    }
}
