<?php

namespace App\Actions\Stripe;

use App\Models\PractitionerSubscriptionDiscount;
use Stripe\Coupon;
use Stripe\StripeClient;

class UpdateSubscriptionDiscount
{
    private StripeClient $stripe;

    private CouponFactory $factory;

    public function __construct(StripeClient $stripe, CouponFactory $factory)
    {
        $this->stripe = $stripe;
        $this->factory = $factory;
    }

    public function execute(
        PractitionerSubscriptionDiscount $discount,
        string $durationType,
        int $rate,
        int $durationInMonths = null
    ): PractitionerSubscriptionDiscount {
        if (!$discount->hasDifferences($durationType, $rate, $durationInMonths)) {
            return $discount;
        }

        $coupon = $this->createCoupon($durationType, $rate, $durationInMonths);
        $this->stripe->subscriptions->update($discount->subscription_id, [
            'coupon' => $coupon->id,
        ]);

        $discount->update([
            'rate' => $rate,
            'duration_type' => $durationType,
            'duration_in_months' => $durationType === PractitionerSubscriptionDiscount::REPEATING_SUBSCRIPTION_TYPE
                ? $durationInMonths
                : null,
            'coupon_id' => $coupon->id,
        ]);
        $discount->save();

        return $discount;
    }

    private function createCoupon(string $durationType, int $rate, int $durationInMonths = null): Coupon
    {
        $params = $this->factory->createCouponParams($durationType, $rate, $durationInMonths);

        return $this->stripe->coupons->create($params);
    }
}
