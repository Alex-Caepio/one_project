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
            'id'             => $myClientPurchase->id,
            'service_name'   => $myClientPurchase->service_name,
            'service_type'   => $myClientPurchase->service_type,
            'schedule_name'  => $myClientPurchase->schedule_name,
            'start_datetime' => new Carbon($myClientPurchase->start_datetime),
            'bookings'       => $myClientPurchase->bookings,
            'full_paid'      => $myClientPurchase->full_paid,
            'refund_terms'   => $myClientPurchase->refund_terms,
        ];
    }
}
