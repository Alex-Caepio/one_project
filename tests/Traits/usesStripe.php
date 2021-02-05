<?php

namespace Tests\Traits;


use App\Models\User;
use Stripe\StripeClient;

trait UsesStripe{

    protected function createStripeClient(User $user)
    {
        $client = app()->make(StripeClient::class);
        $stripeUser =  $client->customers->create(['email' => $user->email]);
        $this->user->stripe_customer_id = $stripeUser->id;
        $this->user->save();

        return $stripeUser->id;
    }

    protected function createStripePaymentMethod(User $user,$cardNumber = null)
    {
        $stripe        = app()->make(StripeClient::class);
        $paymentMethod = $stripe->paymentMethods->create([
            'type' => 'card',
            'card' => [
                'number'    => $cardNumber
                    ? $cardNumber
                    :'4242424242424242',
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
            'recurring' => ['interval' => 'month'],
        ]);
    }

//    protected function createStripePaymentMethod($cardNumber, User $user)
//    {
//        $client        = app()->make(StripeClient::class);
//        $paymentMethod = $client->paymentMethods->create([
//            'type' => 'card',
//            'card' => [
//                'number'    => $cardNumber,
//                'exp_month' => 1,
//                'exp_year'  => 2022,
//                'cvc'       => '314',
//            ],
//        ]);
//
//        $client->paymentMethods->attach($paymentMethod->id, [
//            'customer' => $user->stripe_customer_id
//        ]);
//
//        return $paymentMethod;
}
