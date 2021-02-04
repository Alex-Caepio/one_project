<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentMethod\UpdatePaymentMethodRequest;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\Log;
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
        $cards = $stripe->paymentMethods->all([
            'customer' => Auth::user()->stripe_customer_id,
            'type'     => 'card',
        ]);

        $attachedCard = $stripe->paymentMethods->attach(
            $request->payment_method_id,
            ['customer' => Auth::user()->stripe_customer_id]
        );

        if (count($cards) == 0) {
            $user = Auth::user();

            //if user had no cards prior to that moment, make that new card his primary
            $user->default_payment_method     = $attachedCard->id;
            $user->default_fee_payment_method = $attachedCard->id;
            $user->save();
        }

        return $attachedCard;
    }

    public function detach(StripeClient $stripe, Request $request)
    {
        $stripe->paymentMethods->detach($request->payment_method_id, []);

        return response(null, 204);
    }

    public function update(StripeClient $stripe, UpdatePaymentMethodRequest $request)
    {
        try {
            return $stripe->paymentMethods->update(
                $request->payment_method_id,
                //except payment_method_id and query string
                $request->except(array_merge(['payment_method_id'], array_keys($request->query())))
            );
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::channel('stripe_payment_method_update_error')
                ->info("Client could not update payment method", [
                    'user_id' => $request->user()->id,
                    'payload' => $request->except('payment_method_id'),
                    'message' => $e->getMessage(),
                ]);

            return abort(422, 'Something went wrong');
        }
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
