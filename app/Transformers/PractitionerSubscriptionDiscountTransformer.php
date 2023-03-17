<?php

namespace App\Transformers;

use App\Models\PractitionerSubscriptionDiscount;

class PractitionerSubscriptionDiscountTransformer extends Transformer
{
    protected $availableIncludes = [
        'user',
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(PractitionerSubscriptionDiscount $discount)
    {
        return [
            'id' => $discount->id,
            'user_id' => $discount->user_id,
            'coupon_id' => $discount->coupon_id,
            'subscription_id' => $discount->subscription_id,
            'rate' => $discount->rate,
            'duration_type' => $discount->duration_type,
            'duration_in_months' => $discount->duration_in_months,
            'created_at' => $discount->created_at,
            'updated_at' => $discount->updated_at,
        ];
    }

    public function includeUser(PractitionerSubscriptionDiscount $discount)
    {
        return $this->itemOrNull($discount->user, new UserTransformer());
    }
}
