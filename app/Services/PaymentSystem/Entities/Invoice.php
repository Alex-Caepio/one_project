<?php

namespace App\Services\PaymentSystem\Entities;

use Carbon\Carbon;

class Invoice
{
    public ?string $id = null;

    public ?float $amountDue = null;

    public ?float $amountPaid = null;

    public ?float $amountRemaining = null;

    public ?float $subtotal = null;

    public ?float $total = null;

    public ?string $currency = null;

    public bool $isPaid = false;

    public ?Carbon $paidAt = null;

    public ?string $number = null;

    public ?string $subscriptionId = null;
}
