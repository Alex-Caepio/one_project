<?php

namespace App\Transformers;

use App\Models\Service;
use Carbon\Carbon;

class MyClientClosedTransformer extends Transformer
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
            'service_name'  => $myClientPurchase->service_name,
            'service_id'    => $myClientPurchase->service_id,
            'client_id'     => $myClientPurchase->client_id,
            'booking_id'    => $myClientPurchase->booking_id,
            'service_type'  => $myClientPurchase->service_type,
            'schedule_name' => $myClientPurchase->schedule_name,
            'purchase_date' => new Carbon($myClientPurchase->purchase_date),
            'client'        => $myClientPurchase->client,
            'reference'     => $myClientPurchase->reference,
            'paid'          => $myClientPurchase->paid,
            'closure_date'  => new Carbon($this->getClosureDate($myClientPurchase)),
            'status'        => $myClientPurchase->status,
        ];
    }


    private function getClosureDate($myClientPurchase)
    {
        if ($myClientPurchase->status_full !== 'completed') {
            return $myClientPurchase->cancelled_date;
        } elseif ($myClientPurchase->service_type === Service::TYPE_BESPOKE) {
            return $myClientPurchase->completed_date;
        } elseif ($myClientPurchase->service_type === Service::TYPE_APPOINTMENT) {
            return $myClientPurchase->start_date;
        }
        return $myClientPurchase->closure_date;
    }

}
