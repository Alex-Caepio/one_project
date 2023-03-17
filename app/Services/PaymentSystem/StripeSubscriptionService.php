<?php

namespace App\Services\PaymentSystem;

use App\Services\PaymentSystem\Entities\InvoiceCollection;
use App\Services\PaymentSystem\Stripe\SubscriptionService;
use App\Services\PaymentSystem\Transformers\InvoiceTransformer;

class StripeSubscriptionService implements SubscriptionServiceInterface
{
    private SubscriptionService $service;

    private InvoiceTransformer $transformer;

    public function __construct(SubscriptionService $service, InvoiceTransformer $transformer)
    {
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function getSubscriptionInvoices(string $id): InvoiceCollection
    {
        $invoices = $this->service->getSubscriptionInvoices($id);

        return $this->transformer->transformMany($invoices);
    }
}
