<?php

declare(strict_types=1);

namespace App\DTO\Schedule;

use Stripe\StripeObject;

class PaymentIntendDto
{
    private string $status;
    private ?StripeObject $nextAction;

    public function __construct(string $status, ?StripeObject $nextAction)
    {
        $this->status = $status;
        $this->nextAction = $nextAction;
    }

    public function toArray(): array
    {
        return array_merge([
            'status' => $this->status,
        ], $this->nextAction ? ['next_action' => $this->nextAction->toArray()] : []);
    }
}
