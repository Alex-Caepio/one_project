<?php


namespace App\Transformers;

use App\Models\Purchase;
use League\Fractal\Resource\Item;

class PurchaseTransformer extends Transformer {
    protected $availableIncludes = [
        'schedule',
        'user',
        'price',
        'service',
        'promocode'
    ];

    /**
     * A Fractal transformer.
     *
     * @param \App\Models\Purchase $purchase
     * @return array
     */
    public function transform(Purchase $purchase): array {
        return [
            'id'             => $purchase->id,
            'reference'      => $purchase->reference,
            'user_id'        => $purchase->user_id,
            'schedule_id'    => $purchase->schedule_id,
            'service_id'     => $purchase->service_id,
            'price_id'       => $purchase->price_id,
            'promocode_id'   => $purchase->promocode_id,
            'price_original' => $purchase->price_original,
            'price'          => $purchase->price,
            'created_at'     => $this->dateTime($purchase->created_at),
            'updated_at'     => $this->dateTime($purchase->updated_at),
            'deleted_at'     => $this->dateTime($purchase->deleted_at),
            'is_deposit'     => $purchase->is_deposit,
            'deposit_amount' => $purchase->deposit_amount
        ];
    }

    public function includeSchedule(Purchase $purchase): ?Item {
        return $this->itemOrNull($purchase->schedule, new ScheduleTransformer());
    }

    public function includeService(Purchase $purchase): ?Item {
        return $this->itemOrNull($purchase->service, new ServiceTransformer());
    }

    public function includeUser(Purchase $purchase): ?Item {
        return $this->itemOrNull($purchase->user, new UserTransformer());
    }

    public function includePrice(Purchase $purchase): ?Item {
        return $this->itemOrNull($purchase->price, new PriceTransformer());
    }

    public function includePromocode(Purchase $purchase): ?Item {
        return $this->itemOrNull($purchase->promocode, new PromotionCodeTransformer());
    }

}
