<?php


namespace App\Transformers;

use App\Models\Transfer;

class TransferTransformer extends Transformer
{
    public function transform(Transfer $transfer)
    {
        return [
            'id'                => $transfer->id,
            'user_id'           => $transfer->user_id,
            'stripe_account_id' => $transfer->stripe_account_id,
            'status'            => $transfer->status,
            'amount'            => $transfer->amount,
            'amount_original'   => $transfer->amount_original,
            'currency'          => $transfer->currency,
            'schedule_id'       => $transfer->schedule_id,
            'description'       => $transfer->description,
            'created_at'        => $transfer->created_at,
            'updated_at'        => $transfer->updated_at,
        ];
    }
}
