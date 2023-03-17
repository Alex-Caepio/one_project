<?php

namespace App\Services\PaymentSystem;

use App\Services\PaymentSystem\Entities\InvoiceCollection;

interface SubscriptionServiceInterface
{
    public function getSubscriptionInvoices(string $id): InvoiceCollection;
}
