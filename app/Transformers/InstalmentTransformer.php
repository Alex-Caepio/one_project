<?php


namespace App\Transformers;

use App\Models\Instalment;

class InstalmentTransformer extends Transformer {
    protected $availableIncludes = [
        'user',
        'purchase'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Instalment $instalments) {
        return [
            'id'             => $instalments->id,
            'user_id'        => $instalments->user_id,
            'purchase_id'    => $instalments->purchase_id,
            'payment_date'   => $instalments->payment_date,
            'is_paid'        => $instalments->is_paid,
            'payment_amount' => $instalments->payment_amount,
            'created_at'     => $instalments->created_at,
            'updated_at'     => $instalments->updated_at,
            'deleted_at'     => $instalments->deleted_at,
        ];
    }

    public function includeUser(Instalment $instalments) {
        return $this->itemOrNull($instalments->user, new UserTransformer());
    }

    public function includePurchase(Instalment $instalments) {
        return $this->itemOrNull($instalments->purchase, new PurchaseTransformer());
    }
}
