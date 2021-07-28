<?php

namespace App\Transformers;

use Carbon\Carbon;

class MyClientClosedTransformer extends Transformer {
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($myClientPurchase) {
        return [
            'id'            => $myClientPurchase->id,
            'service_name'  => $myClientPurchase->service_name,
            'service_id'    => $myClientPurchase->service_id,
            'client_id'     => $myClientPurchase->client_id,
            'service_type'  => $myClientPurchase->service_type,
            'schedule_name' => $myClientPurchase->schedule_name,
            'purchase_date' => new Carbon($myClientPurchase->purchase_date),
            'client'        => $myClientPurchase->client,
            'reference'     => $myClientPurchase->reference,
            'paid'          => $myClientPurchase->paid,
            'closure_date'  => new Carbon($myClientPurchase->closure_date),
            'status'        => $myClientPurchase->status,
        ];
    }
}
