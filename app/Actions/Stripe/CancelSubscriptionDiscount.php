<?php

namespace App\Actions\Stripe;

use App\Models\PractitionerSubscriptionDiscount;
use Stripe\StripeClient;

class CancelSubscriptionDiscount
{
    private StripeClient $stripe;

    public function __construct(StripeClient $stripe)
    {
        $this->stripe = $stripe;
    }

    public function execute(PractitionerSubscriptionDiscount $discount)
    {
        $this->stripe->subscriptions->deleteDiscount($discount->subscription_id);
        $this->stripe->coupons->delete($discount->coupon_id);
        $discount->delete();
    }
}
