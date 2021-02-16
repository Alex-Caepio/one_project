<?php


namespace App\Actions\Cancellation;

use App\Models\Booking;
use App\Models\Cancellation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

/**
 * Class CancelBooking
 * https://stripe.com/docs/connect/account-debits#charging-a-connected-account
 *
 * @package App\Actions\Cancellation
 */
class CancelBooking {
    private StripeClient $stripe;

    public function execute(Booking $booking) {
        $this->stripe = app()->make(StripeClient::class);

        try {
            $booking->load(['practitioner', 'purchase']);
            if (!$booking->purchase || !$booking->practitioner) {
                throw new \Exception('Incorrect model relation in booking #' . $booking->id);
            }
            $paymentIntent = $this->stripe->paymentIntents->retrieve($booking->purchase->stripe_id);
            $stripeRefund = $this->stripe->refunds->create([
                                                               'amount'         => $booking->cost,
                                                               'payment_intent' => $paymentIntent->id
                                                           ]);
            $totalFee = 0;
            if (Auth::user()->isPractitioner()) {
                $totalFee = round(((double)$booking->cost / 100) * (int)config('app.platform_cancellation_fee'));
            }

            $booking->cancelled_at = Carbon::now();
            $booking->save();

            $cancellation = new Cancellation();
            $cancellation->fill([
                                    'user_id'             => $booking->user_id,
                                    'booking_id'          => $booking->id,
                                    'purchase_id'         => $booking->purchase_id,
                                    'practitioner_id'     => $booking->practitioner_id,
                                    'amount'              => $booking->cost,
                                    'fee'                 => $totalFee > 0 ? $totalFee : null,
                                    'cancelled_by_client' => Auth::user()->isClient(),
                                    'stripe_id'           => $stripeRefund->id
                                ]);
            $cancellation->save();

            $chargeAmount = $booking->cost + $totalFee;

            $this->stripe->charges->create([
                                               'amount'                      => $chargeAmount,
                                               'currency'                    => config('app.platform_currency'),
                                               'source'                      => $booking->practitioner->stripe_account_id,
                                               'statement_descriptor_suffix' => $booking->reference
                                           ]);

            Log::channel('stripe_refund_success')->info('Stripe refund success: ', [
                'user_id'                     => $booking->user_id ?? null,
                'practitioner_id'             => $booking->practitioner_id ?? null,
                'booking_id'                  => $booking->id ?? null,
                'refund_amount'               => $booking->cost,
                'charge_amount'               => $chargeAmount,
                'payment_stripe'              => $booking->purchase->stripe_id ?? null,
                'refund_stripe_id'            => $stripeRefund->id,
                'statement_descriptor_suffix' => $booking->reference
            ]);
        } catch (ApiErrorException $e) {
            Log::channel('stripe_refund_fail')->info('Stripe refund error: ', [
                'user_id'         => $booking->user_id ?? null,
                'practitioner_id' => $booking->practitioner_id ?? null,
                'booking_id'      => $booking->id ?? null,
                'refund_amount'   => $booking->cost,
                'payment_stripe'  => $booking->purchase->stripe_id ?? null,
                'message'         => $e->getMessage(),
            ]);
            return abort(500);
        }
        return response(null, 204);
    }
}
