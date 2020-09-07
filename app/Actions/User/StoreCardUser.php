<?php


namespace App\Actions\User;


use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;

class StoreCardUser
{
    public function execute()
    {
        $stripe_id = Auth::user()->stripe_id;
        $stripe = app(StripeClient::class);
        $stripe->customers->createSource($stripe_id, ['source' => 'tok_visa']);
    }
}
