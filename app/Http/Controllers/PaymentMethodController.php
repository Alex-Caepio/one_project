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

    public function default(StripeClient $stripe, Request $request) {

        $paymentMethods = $stripe->customers->update(
            Auth::user()->stripe_customer_id,
            ['default_source' => $request->payment_method_id]
        );

        return $paymentMethods;
    }

    public function update(StripeClient $stripe, Request $request) {

        $paymentMethods = $stripe->paymentMethods->update(
            $request->payment_method_id,
            $request->except('payment_method_id')
        );

        return $paymentMethods;
    }

    public function defaultFee(Request $request) {

        $user = Auth::user();
        $user->default_fee_payment_method = $request->payment_method_id;
        $user->save();

        return response(null, 204);
    }

    public function detach(StripeClient $stripe, Request $request) {
        $stripe->paymentMethods->detach($request->payment_method_id, []);

        return response(null, 204);
    }
}
