<?php

namespace App\Actions\Stripe;

use App\Models\PractitionerSubscriptionDiscount;

class CouponFactory
{
    public function createCouponParams(string $durationType, int $rate, int $durationInMonths = null): array
    {
        return $durationType === PractitionerSubscriptionDiscount::REPEATING_SUBSCRIPTION_TYPE
            ? $this->createRepeating($rate, $durationInMonths)
            : $this->createForever($rate);
    }

    public function createRepeating(int $rate, int $durationInMonths): array
    {
        return [
            'amount_off' => $rate * 100,
            'currency' => config('app.platform_currency'),
            'duration' => 'repeating',
            'duration_in_months' => $durationInMonths,
        ];
    }

    public function createForever(int $rate): array
    {
        return [
            'amount_off' => $rate * 100,
            'currency' => config('app.platform_currency'),
            'duration' => 'forever',
        ];
    }
}
