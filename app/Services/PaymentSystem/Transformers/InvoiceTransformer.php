<?php

namespace App\Services\PaymentSystem\Transformers;

use App\Services\PaymentSystem\Entities\Invoice;
use App\Services\PaymentSystem\Entities\InvoiceCollection;
use Carbon\Carbon;

class InvoiceTransformer
{
    public function transformMany(iterable $data): InvoiceCollection
    {
        $items = [];

        foreach ($data as $item) {
            $items[] = $this->transform($item);
        }

        return new InvoiceCollection($items);
    }

    public function transform(array $data): Invoice
    {
        $invoice = new Invoice();

        $invoice->id = $data['id'];
        $invoice->amountDue = $this->convertMoney($data['amount_due']);
        $invoice->amountPaid = $this->convertMoney($data['amount_paid']);
        $invoice->amountRemaining = $this->convertMoney($data['amount_remaining']);
        $invoice->currency = $data['currency'];
        $invoice->number = $data['number'];
        $invoice->isPaid = $data['paid'];
        $invoice->subtotal = $this->convertMoney($data['subtotal']);
        $invoice->total = $this->convertMoney($data['total']);
        $invoice->paidAt = Carbon::parse($data['created']);
        $invoice->subscriptionId = $data['subscription'];

        return $invoice;
    }

    private function convertMoney(float $number): float
    {
        return $number / 100;
    }
}
