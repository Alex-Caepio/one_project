<?php

namespace App\Services\PaymentSystem;

use App\Services\PaymentSystem\Entities\InvoiceCollection;
use App\Services\PaymentSystem\Stripe\TransferService;

class StripeSubscriptionTransferDecorator implements SubscriptionServiceInterface
{
    private SubscriptionServiceInterface $service;

    private TransferService $transferService;

    public function __construct(SubscriptionServiceInterface $service, TransferService $transferService)
    {
        $this->service = $service;
        $this->transferService = $transferService;
    }

    public function getSubscriptionInvoices(string $id): InvoiceCollection
    {
        $invoices = $this->service->getSubscriptionInvoices($id);

        foreach ($invoices as $invoice) {
            $invoice->transfer = $this->transferService->getTransferOfInvoice($invoice->id);
        }

        return $invoices;
    }
}
