<?php

namespace App\Actions\Promo;

use App\Models\Promotion;
use App\Models\PromotionCode;

class CalculatePromoPrice
{
    public function execute(PromotionCode $promo, $amount, $cost)
    {
        $promotion = $promo->promotion;
        $promoValue = $promotion->discount_value;
        $total = $amount * $cost;
        return $promotion->discount_type === Promotion::TYPE_PERCENTAGE ?
            $total - ($total * ($promoValue / 100)) :
            $total - $promoValue;
    }
}
