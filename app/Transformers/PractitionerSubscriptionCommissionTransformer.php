<?php


namespace App\Transformers;


use App\Models\PractitionerSubscriptionCommission;

class PractitionerSubscriptionCommissionTransformer extends Transformer
{
    protected $availableIncludes = [
        'user',
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(PractitionerSubscriptionCommission $practitionerSubscriptionCommission)
    {
        return [
            'id'                 => $practitionerSubscriptionCommission->id,
            'user_id'            => $practitionerSubscriptionCommission->user_id,
            'rate'               => $practitionerSubscriptionCommission->rate,
            'commission_on_sale' => $practitionerSubscriptionCommission->commission_on_sale,
            'date_from'          => $practitionerSubscriptionCommission->date_from,
            'date_to'            => $practitionerSubscriptionCommission->date_to,
            'is_dateless'        => $practitionerSubscriptionCommission->is_dateless,
            'created_at'         => $practitionerSubscriptionCommission->created_at,
            'updated_at'         => $practitionerSubscriptionCommission->updated_at,
            'stripe_coupon_id'   => $practitionerSubscriptionCommission->stripe_coupon_id,
            'subscription_schedule_id' => $practitionerSubscriptionCommission->subscription_schedule_id,
        ];
    }

    public function includeUser(PractitionerSubscriptionCommission $practitionerSubscriptionCommission)
    {
        return $this->itemOrNull($practitionerSubscriptionCommission->user, new UserTransformer());
    }
}
