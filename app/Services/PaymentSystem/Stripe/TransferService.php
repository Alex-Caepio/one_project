<?php

namespace App\Services\PaymentSystem\Stripe;

use App\Services\PaymentSystem\Entities\Transfer;
use App\Services\PaymentSystem\Transformers\TransferByChargeTransformer;
use Stripe\StripeClient;

class TransferService
{
    private StripeClient $stripe;

    private TransferByChargeTransformer $transformer;

    public function __construct(StripeClient $stripe, TransferByChargeTransformer $transformer)
    {
        $this->stripe = $stripe;
        $this->transformer = $transformer;
    }

    public function getTransferOfInvoice(string $invoiceId): ?Transfer
    {
        $invoice = $this->stripe->invoices->retrieve($invoiceId);

        if (!$invoice->payment_intent) {
            return null;
        }

        $payment = $this->stripe->paymentIntents->retrieve($invoice->payment_intent);

        return $this->transformer->transform($payment->charges->first()->toArray());
    }
}
