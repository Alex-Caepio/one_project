<?php


namespace App\Actions\User;


use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;

class GetCreditCards
{
    public function execute(StripeClient $stripe)
    {
        $stripeId = Auth::user()->stripe_customer_id;
        return $stripe->customers->allSources(
            $stripeId,
            ['object' => 'card', 'limit' => 99]
        );
    }
}
