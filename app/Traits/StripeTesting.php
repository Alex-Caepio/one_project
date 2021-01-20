<?php

namespace App\Traits;


use App\Models\User;
use phpDocumentor\Reflection\Types\Null_;
use Stripe\StripeClient;

trait StripeTesting{

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
