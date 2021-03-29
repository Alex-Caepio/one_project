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
            'service_name'  => $myClientPurchase->service_name,
            'service_type'  => $myClientPurchase->service_type,
            'schedule_name' => $myClientPurchase->schedule_name,
            'purchase_date' => new Carbon($myClientPurchase->purchase_date),
            'client'        => $myClientPurchase->client,
            'paid'          => $myClientPurchase->paid,
            'purchased'     => 1,
            'location'      => $myClientPurchase->location,
            'refund_terms'  => $myClientPurchase->refund_terms,
            'reference'     => $myClientPurchase->reference
        ];
    }
}
