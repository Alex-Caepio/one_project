<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Auth;

class PaymentMethodController extends Controller
{
    public function index(StripeClient $stripe)
    {
        return $stripe->paymentMethods->all([
            'customer' => Auth::user()->stripe_customer_id,
            'type'     => 'card',
        ]);
    }

    public function attach(StripeClient $stripe, Request $request)
    {
        return $stripe->paymentMethods->attach(
            $request->payment_method_id,
            ['customer' => Auth::user()->stripe_customer_id]
        );
    }

    public function detach(StripeClient $stripe, Request $request)
    {
        $stripe->paymentMethods->detach($request->payment_method_id, []);

        return response(null, 204);
    }

    public function update(StripeClient $stripe, Request $request)
    {
        return $stripe->paymentMethods->update(
            $request->payment_method_id,
            $request->except('payment_method_id')
        );
    }

    /**
     * Sets default payment method
     */
    public function default(StripeClient $stripe, Request $request)
    {
        $user = Auth::user();

        $user->default_payment_method = $request->payment_method_id;
        $user->save();

        return response(null, 204);
    }

    /**
     * Sets default payment method for fees only
     */
    public function defaultFee(Request $request)
    {
        $user = Auth::user();

        $user->default_fee_payment_method = $request->payment_method_id;
        $user->save();

        return response(null, 204);
    }
}
