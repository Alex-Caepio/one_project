<?php


namespace App\Actions\User;


use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;

class AllCardUser
{
    public function execute(StripeClient $stripe)
    {
        $stripe_id = Auth::user()->stripe_customer_id;
        return $stripe->customers->allSources(
            $stripe_id,
            ['object' => 'card', 'limit' => 99]
        );
    }
}
