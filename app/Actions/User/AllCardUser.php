<?php


namespace App\Actions\User;


use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;

class AllCardUser
{
    public function execute($allCards)
    {
        $stripe_id = Auth::user()->stripe_id;
        $stripe = app(StripeClient::class);
        $allCards = $stripe->customers->allSources(
            $stripe_id,
            ['object' => 'card', 'limit' => 99]
        );
        return $allCards;
    }
}
