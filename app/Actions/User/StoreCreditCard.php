<?php

namespace App\Actions\User;

use Stripe\StripeClient;
use Illuminate\Support\Facades\Auth;

class StoreCreditCard
{
    public function execute(StripeClient $stripe)
    {
        $stripe_id = Auth::user()->stripe_customer_id;
        $stripe->customers->createSource($stripe_id, ['source' => 'tok_visa']);
    }
}
