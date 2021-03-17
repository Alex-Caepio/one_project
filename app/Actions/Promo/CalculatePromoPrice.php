<?php

namespace App\Actions\Promo;

use App\Models\Promotion;
use App\Models\PromotionCode;

class CalculatePromoPrice {
    public function execute(PromotionCode $promo, $scheduleCost) {
        $promotion = $promo->promotion;
        $promoValue = $promotion->discount_value;

        return $promotion->discount_type === Promotion::TYPE_PERCENTAGE ? $scheduleCost - ($scheduleCost *
                                                                                           ($promoValue /
                                                                                            100)) : $scheduleCost -
                                                                                                    $promoValue;
    }
}
