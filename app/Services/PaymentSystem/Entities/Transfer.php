<?php

namespace App\Services\PaymentSystem\Entities;

use Carbon\Carbon;

class Transfer
{
    /**
     * A transfer ID: tr_N.
     */
    public ?string $id = null;

    public ?float $amount = null;

    public ?string $currency = null;

    public ?string $destansion = null;

    public ?Carbon $createdAt = null;
}
