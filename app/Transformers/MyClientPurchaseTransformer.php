<?php

namespace App\Transformers;

use Carbon\Carbon;

class MyClientPurchaseTransformer extends Transformer
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($myClientPurchase)
    {
        return [
            'id'            => $myClientPurchase->id,
            'booking_id'    => $myClientPurchase->booking_id,
            'service_id'    => $myClientPurchase->service_id,
            'service_name'  => $myClientPurchase->service_name,
            'service_type'  => $myClientPurchase->service_type,
            'schedule_name' => $myClientPurchase->schedule_name,
            'deposit_amount'=> $myClientPurchase->deposit_amount,
            'is_deposit'    => $myClientPurchase->is_deposit,
            'purchase_date' => new Carbon($myClientPurchase->purchase_date),
            'client'        => $myClientPurchase->client,
            'price'         => $myClientPurchase->price,
            'paid'          => $myClientPurchase->paid,
            'purchased'     => $myClientPurchase->amount,
            'location'      => $myClientPurchase->location,
            'city'          => $myClientPurchase->city,
            'country'       => $myClientPurchase->country,
            'url'           => $myClientPurchase->url,
            'refund_terms'  => $myClientPurchase->refund_terms,
            'reference'     => $myClientPurchase->reference
        ];
    }
}
