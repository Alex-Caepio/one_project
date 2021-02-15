<?php


namespace App\Actions\Cancellation;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;

class CancelBooking {
    private StripeClient $stripe;

    public function execute(Booking $booking) {
        $this->stripe = app()->make(StripeClient::class);
        $booking->load(['practitioner', 'purchase']);
        if ($booking->purchase && $booking->practitioner) {
            $paymentIntent = $this->stripe->paymentIntents->retrieve($booking->purchase->stripe_id);
            $this->stripe->refunds->create([
                                               'amount'         => $paymentIntent->amount,
                                               'payment_intent' => $paymentIntent->id
                                           ]);
            $totalFee = 0;
            if (Auth::user()->isPractitioner()) {
                $totalFee =
                    round(((double)$paymentIntent->amount / 100) * (int)config('app.platform_cancenllation_fee'));
            }
            if ($totalFee > 0) {
                $this->stripe->charges->create([
                                                   'amount'     => $paymentIntent->amount + $totalFee,
                                                   'account_id' => $booking->practitioner->stripe_account_id
                                               ]);
            }
        }
        return response(null, 204);
    }
}
