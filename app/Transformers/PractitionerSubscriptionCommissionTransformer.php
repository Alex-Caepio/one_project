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
    public function transform(PractitionerSubscriptionCommission $commission)
    {
        return [
            'id'                 => $commission->id,
            'user_id'            => $commission->user_id,
            'rate'               => $commission->rate,
            'commission_on_sale' => $commission->commission_on_sale,
            'date_from'          => is_null($commission->date_from) ? null : $commission->date_from->format('Y-m-d'),
            'date_to'            => is_null($commission->date_to) ? null : $commission->date_to->format('Y-m-d'),
            'is_dateless'        => $commission->is_dateless,
            'created_at'         => $commission->created_at,
            'updated_at'         => $commission->updated_at,
            'stripe_coupon_id'   => $commission->stripe_coupon_id,
            'subscription_schedule_id' => $commission->subscription_schedule_id,
        ];
    }

    public function includeUser(PractitionerSubscriptionCommission $practitionerSubscriptionCommission)
    {
        return $this->itemOrNull($practitionerSubscriptionCommission->user, new UserTransformer());
    }
}
