<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;

class PaymentMethodController extends Controller {

    public function index(StripeClient $stripe) {
        $paymentMethods = $stripe->paymentMethods->all([
            'customer' => Auth::user()->stripe_customer_id,
            'type' => 'card',
        ]);
        return $paymentMethods;
    }

    public function attach(StripeClient $stripe, Request $request) {
        $paymentMethods = $stripe->paymentMethods->attach(
            $request->payment_method_id,
            ['customer' => Auth::user()->stripe_customer_id]
        );

        return $paymentMethods;
    }

    public function detach(StripeClient $stripe, Request $request) {

        $stripe->paymentMethods->detach(
            $request->payment_method_id,
            ['customer' => Auth::user()->stripe_customer_id]
        );

        return response(null, 204);
    }
}
