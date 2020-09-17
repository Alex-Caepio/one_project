<?php


namespace App\Actions\Promo;


class CalculatePromoPrice
{
    public function execute($promo, $scheduleCost)
    {
        $promotion = $promo->promotion;
        $percentage = $promotion->where('discount_type', 'percentage');
        $promoValue = $promotion->discount_value;
        if ($percentage) {
            $newSchedule = $scheduleCost - ($scheduleCost * ($promoValue / 100));

        } else {
            $newSchedule = $scheduleCost - $promoValue;
        }
        return $newSchedule;
    }
}
