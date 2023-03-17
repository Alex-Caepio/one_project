<?php

namespace App\Actions\Stripe;

use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;

class GetViablePaymentMethod
{
    public function execute($practitoner, $prefferedPaymentMethod = null)
    {
        $stripe = app()->make(StripeClient::class);
        $cards = $stripe->paymentMethods->all([
            'customer' => Auth::user()->stripe_customer_id,
            'type'     => 'card',
        ]);

        switch (true) {
            case $prefferedPaymentMethod:
                return $prefferedPaymentMethod;

            case $practitoner->default_payment_method:
                $payment_method_id = $practitoner->default_payment_method;
                return $payment_method_id;

            case count($cards):
                $payment_method_id = $cards->data[0]->id;
                return $payment_method_id;

            case 422:
                return abort(422, ['message' => 'You have no viable payment methods to pay']);
        }
    }
}
