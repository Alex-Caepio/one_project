<?php


namespace App\Transformers;

use App\Models\Purchase;

class PurchaseTransformer extends Transformer
{
    protected $availableIncludes = [
        'schedule', 'user', 'price'
    ];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Purchase $purchases)
    {
        return [
            'id'                => $purchases->id,
            'user_id'           => $purchases->user_id,
            'schedule_id'       => $purchases->schedule_id,
            'price_id'          => $purchases->price_id,
            'promocode'         => $purchases->promocode,
            'price_original'    => $purchases->price_original,
            'price'             => $purchases->price,
            'created_at'        => $purchases->created_at,
            'updated_at'        => $purchases->updated_at,
            'deleted_at'        => $purchases->deleted_at,
        ];
    }

    public function includeSchedule(Purchase $purchases)
    {
        return $this->itemOrNull($purchases->schedule, new ScheduleTransformer());
    }

    public function includeUser(Purchase $purchases)
    {
        return $this->itemOrNull($purchases->user, new UserTransformer());
    }

    public function includePrice(Purchase $purchases)
    {
        return $this->itemOrNull($purchases->price, new PriceTransformer());
    }
}
