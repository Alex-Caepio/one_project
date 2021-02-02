<?php

namespace App\Transformers;

use App\Models\Cancellation;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class CancellationTransformer extends TransformerAbstract {

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'user',
        'practitioner',
        'booking',
        'purchases'
    ];

    /**
     * A Fractal transformer.
     *
     * @param \App\Models\Cancellation $cancellation
     * @return array
     */
    public function transform(Cancellation $cancellation): array {
        return [
            'id'                  => $cancellation->id,
            'user_id'             => $cancellation->user_id,
            'booking_id'          => $cancellation->booking_id,
            'purchase_id'         => $cancellation->purchase_id,
            'practitioner_id'     => $cancellation->practitioner_id,
            'amount'              => $cancellation->amount,
            'fee'                 => $cancellation->fee,
            'cancelled_by_client' => $cancellation->cancelled_by_client,
            'stripe_id'           => $cancellation->stripe_id,
            'created_at'          => $this->dateTime($cancellation->created_at),
            'updated_at'          => $this->dateTime($cancellation->updated_at),
        ];
    }

    public function includeUser(Cancellation $cancellation): ?Item {
        return $this->itemOrNull($cancellation->user, new UserTransformer());
    }

    public function includePractitioner(Cancellation $cancellation): ?Item {
        return $this->itemOrNull($cancellation->practitioner, new UserTransformer());
    }

    public function includeBooking(Cancellation $cancellation): ?Item {
        return $this->itemOrNull($cancellation->booking, new BookingTransformer());
    }

    public function includePurchase(Cancellation $cancellation): ?Item {
        return $this->itemOrNull($cancellation->purchase, new PurchaseTransformer());
    }
}
