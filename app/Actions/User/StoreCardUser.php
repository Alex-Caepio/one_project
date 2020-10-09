<?php


namespace App\Actions\User;


use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;

class StoreCardUser
{
    public function execute(StripeClient $stripe)
    {
        $stripe_id = Auth::user()->stripe_customer_id;
        $stripe->customers->createSource($stripe_id, ['source' => 'tok_visa']);
    }
}
