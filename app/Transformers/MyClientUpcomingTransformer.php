<?php

namespace App\Transformers;

use Carbon\Carbon;

class MyClientUpcomingTransformer extends Transformer
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($myClientPurchase)
    {
        return [
            'id'                 => $myClientPurchase->id,
            'service_id'         => $myClientPurchase->service_id,
            'service_name'       => $myClientPurchase->service_name,
            'service_type'       => $myClientPurchase->service_type,
            'schedule_name'      => $myClientPurchase->schedule_name,
            'start_datetime'     => new Carbon($myClientPurchase->start_datetime),
            'bookings'           => $myClientPurchase->bookings,
            'has_installments'   => (int)$myClientPurchase->bookings_with_installment > 0,
            'installments_count' => $myClientPurchase->bookings_with_installment,
            'full_paid'          => $myClientPurchase->full_paid,
            'refund_terms'       => $myClientPurchase->refund_terms,
        ];
    }
}
