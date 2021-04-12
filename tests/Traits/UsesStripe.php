<?php

namespace Tests\Traits;

use App\Models\User;
use Stripe\StripeClient;

trait UsesStripe
{

    protected function createConnectAccount(User $user)
    {
        $client                  = app()->make(StripeClient::class);
        $connectAccount          = $client->accounts->create([
            'type'    => 'standard',
            'country' => 'US',
            'email'   => $user->email,
        ]);
        $user->stripe_account_id = $connectAccount->id;
        $user->save();

        return $connectAccount->id;
    }

    protected function createStripeClient(User $user)
    {
        $client                   = app()->make(StripeClient::class);
        $stripeUser               = $client->customers->create(['email' => $user->email]);
        $user->stripe_customer_id = $stripeUser->id;
        $user->save();

        return $stripeUser->id;
    }

    protected function createStripePaymentMethod(User $user, $cardNumber = null)
    {
        $stripe        = app()->make(StripeClient::class);
        $paymentMethod = $stripe->paymentMethods->create([
            'type' => 'card',
            'card' => [
                'number'    => $cardNumber
                    ? $cardNumber
                    : '4000000000000077 ',
                'exp_month' => 1,
                'exp_year'  => 2022,
                'cvc'       => '314',
            ],
        ]);


        $paymentMethod = $stripe->paymentMethods->attach($paymentMethod->id, [
            'customer' => $user->stripe_customer_id
        ]);

        return $paymentMethod;
    }

    protected function createStripeProduct()
    {
        $client = app()->make(StripeClient::class);
        return $client->products->create(['name' => 'Test product @' . now()->toDateTimeString()]);
    }

    protected function createStripeRecurringPrice($stripeProductId)
    {
        $client = app()->make(StripeClient::class);
        return $client->prices->create([
            'unit_amount' => '1000',
            'currency'    => 'usd',
            'product'     => $stripeProductId,
            'recurring'   => ['interval' => 'month'],
        ]);
    }
}
