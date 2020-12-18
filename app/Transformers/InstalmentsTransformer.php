<?php


namespace App\Transformers;

use App\Models\Instalments;

class InstalmentsTransformer extends Transformer
{
    protected $availableIncludes = [
        'schedule', 'user', 'price',
        'purchase'
    ];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Instalments $instalments)
    {
        return [
            'id'                => $instalments->id,
            'user_id'           => $instalments->user_id,
            'schedule_id'       => $instalments->schedule_id,
            'price_id'          => $instalments->price_id,
            'purchase_id'       => $instalments->purchase_id,
            'payment_date'      => $instalments->payment_date,
            'is_paid'           => $instalments->is_paid,
            'payment_amount'    => $instalments->payment_amount,
            'created_at'        => $instalments->created_at,
            'updated_at'        => $instalments->updated_at,
            'deleted_at'        => $instalments->deleted_at,
        ];
    }

    public function includeSchedules(Instalments $instalments)
    {
        return $this->collectionOrNull($instalments->schedule, new ScheduleTransformer());
    }

    public function includeUsers(Instalments $instalments)
    {
        return $this->itemOrNull($instalments->user, new UserTransformer());
    }

    public function includePrices(Instalments $instalments)
    {
        return $this->itemOrNull($instalments->price, new PriceTransformer());
    }

    public function includePurchases(Instalments $instalments)
    {
        return $this->itemOrNull($instalments->purchase, new PurchasesTransformer());
    }
}
