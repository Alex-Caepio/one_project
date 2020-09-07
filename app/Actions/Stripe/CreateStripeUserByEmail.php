<?php

namespace App\Actions\Stripe;

use Stripe\StripeClient;

class CreateStripeUserByEmail
{
    public function execute(string $email)
    {
        $stripe = app(StripeClient::class);
        return $stripe->customers->create(['email' => $email]);
    }
}
