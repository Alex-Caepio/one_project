<?php

namespace App\Services\PaymentSystem\Transformers;

use App\Services\PaymentSystem\Entities\Transfer;
use Carbon\Carbon;

class TransferByChargeTransformer
{
    public function transform(array $data): Transfer
    {
        $transfer = new Transfer();

        $transfer->id = $data['transfer'];
        $transfer->amount = $this->convertMoney($data['transfer_data']['amount'] ?? $data['amount']);
        $transfer->currency = $data['currency'];
        $transfer->destansion = $data['destination'];
        $transfer->createdAt = Carbon::parse($data['created']);

        return $transfer;
    }

    private function convertMoney(float $number): float
    {
        return $number / 100;
    }
}
