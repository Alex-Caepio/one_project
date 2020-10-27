<?php


namespace App\Actions\Promo;


class CalculatePromoPrice
{
    public function execute($promo, $scheduleCost)
    {
        $promotion = $promo->promotion;
        $isPercentage = $promotion->discount_type == 'percentage';
        $promoValue = $promotion->discount_value;

        return $isPercentage
            ? $scheduleCost - ($scheduleCost * ($promoValue / 100))
            : $scheduleCost - $promoValue;
    }
}
