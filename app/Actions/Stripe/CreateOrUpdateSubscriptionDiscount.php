<?php

namespace App\Actions\Stripe;

use App\Models\PractitionerSubscriptionDiscount;
use App\Models\User;
use Stripe\Coupon;
use Stripe\StripeClient;

class CreateOrUpdateSubscriptionDiscount
{
    private StripeClient $stripe;

    private CouponFactory $factory;

    public function __construct(StripeClient $stripe, CouponFactory $factory)
    {
        $this->stripe = $stripe;
        $this->factory = $factory;
    }

    public function execute(
        User $user,
        string $durationType,
        int $rate,
        int $durationInMonths = null
    ): PractitionerSubscriptionDiscount {
        $user->loadMissing('plan');

        $coupon = $this->createCoupon($durationType, $rate, $durationInMonths);
        $this->stripe->subscriptions->update($user->stripe_plan_id, [
            'coupon' => $coupon->id,
        ]);

        return $user->practitionerSubscriptionDiscount()->updateOrCreate([
            'user_id' => $user->id,
        ], [
            'rate' => $rate,
            'duration_type' => $durationType,
            'duration_in_months' => $durationType === PractitionerSubscriptionDiscount::REPEATING_SUBSCRIPTION_TYPE
                ? $durationInMonths
                : null,
            'subscription_id' => $user->stripe_plan_id,
            'coupon_id' => $coupon->id,
        ]);
    }

    private function createCoupon(string $durationType, int $rate, int $durationInMonths = null): Coupon
    {
        $params = $this->factory->createCouponParams($durationType, $rate, $durationInMonths);

        return $this->stripe->coupons->create($params);
    }
}
