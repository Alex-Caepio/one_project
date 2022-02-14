<?php

namespace App\Actions\Promo;

use App\Models\Promotion;
use App\Models\PromotionCode;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class PromoIsAvailable
{
    const INSUFFICIENT_BALANCE = 'Operation could not be performed';

    public function execute(PromotionCode $promo, $amount, $cost)
    {
        $price = run_action(CalculatePromoPrice::class, $promo, $amount, $cost);
        $discount = $cost * $amount - $price;
        $needToTransfer = $price + $discount / ($promo->promotion->applied_to == Promotion::APPLIED_HOST ? 1 : 2);

        try {
            $balance = (app()->make(StripeClient::class))->balance->retrieve();
        } catch (ApiErrorException $e) {
            Log::channel('stripe_get_balance_error')
                ->info("Could not get holistify balance", [
                    'need_to_transfer' => $needToTransfer,
                    'message' => $e->getMessage(),
                ]);
            return false;
        }

        if (!empty($balance->available)) {
            $balanceAmount = array_shift($balance->available)->amount;
        }

        if ($balanceAmount >= $needToTransfer) {
            return true;
        }

        return false;
    }
}
