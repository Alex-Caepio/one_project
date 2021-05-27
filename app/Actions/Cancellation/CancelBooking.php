<?php


namespace App\Actions\Cancellation;

use App\Events\BookingCancelledByPractitioner;
use App\Events\BookingCancelledByClient;
use App\Http\Requests\Cancellation\CancelBookingRequest;
use App\Models\Booking;
use App\Models\Cancellation;
use App\Models\Notification;
use App\Models\RescheduleRequest;
use App\Models\User;
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

    public function execute(Booking $booking, bool $declineRescheduleRequest = false,
                            ?CancelBookingRequest $request = null) {
        $this->stripe = app()->make(StripeClient::class);
        $booking->load(['user', 'practitioner', 'purchase', 'schedule', 'schedule.service']);
        if (!$booking->purchase || !$booking->practitioner) {
            throw new \Exception('Incorrect model relation in booking #' . $booking->id);
        }

        $totalFee = 0;
        $stripeRefund = null;

        if ($request !== null && $request->filled('role')) {
            $actionRole = $request->get('role');
        } else {
            $actionRole = Auth::id() === $booking->user_id ? User::ACCOUNT_CLIENT : User::ACCOUNT_PRACTITIONER;
        }

        if ($actionRole === User::ACCOUNT_CLIENT) {
            $refundValue = $this->clientRefund($booking, $declineRescheduleRequest);
        } else {
            $refundValue = $booking->cost;
            $totalFee = round(((double)$booking->cost / 100) * (int)config('app.platform_cancellation_fee'));
        }

        $chargeAmount = $refundValue + $totalFee;

        if ($refundValue > 0) {
            try {
                $paymentIntent = $this->stripe->paymentIntents->retrieve($booking->purchase->stripe_id);
                $stripeRefund = $this->stripe->refunds->create([
                                                                   'amount'         => $refundValue,
                                                                   'payment_intent' => $paymentIntent->id
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

                if ($chargeAmount > 0 && $booking->practitioner->stripe_account_id) {
                    $this->stripe->charges->create([
                                                       'amount'                      => $chargeAmount,
                                                       'currency'                    => config('app.platform_currency'),
                                                       'source'                      => $booking->practitioner->stripe_account_id,
                                                       'statement_descriptor_suffix' => $booking->reference
                                                   ]);
                }

            } catch (ApiErrorException $e) {
                Log::channel('stripe_refund_fail')->info('Stripe refund error: ', [
                    'user_id'         => $booking->user_id ?? null,
                    'practitioner_id' => $booking->practitioner_id ?? null,
                    'booking_id'      => $booking->id ?? null,
                    'payment_stripe'  => $booking->purchase->stripe_id ?? null,
                    'message'         => $e->getMessage(),
                ]);
            }
        }

        if($booking->is_installment){
            $this->refundInstallment($booking->purchase->subscription_id);
        }

        $booking->cancelled_at = Carbon::now();
        $booking->status = 'canceled';
        $booking->save();

        $cancellation = new Cancellation();
        $cancellation->fill([
                                'user_id'             => $booking->user_id,
                                'booking_id'          => $booking->id,
                                'purchase_id'         => $booking->purchase_id,
                                'practitioner_id'     => $booking->practitioner_id,
                                'amount'              => $refundValue,
                                'fee'                 => $totalFee > 0 ? $totalFee : null,
                                'cancelled_by_client' => $actionRole === User::ACCOUNT_CLIENT,
                                'stripe_id'           => $stripeRefund->id ?? null
                            ]);
        $cancellation->save();

        $notification = new Notification();

        if ($actionRole === User::ACCOUNT_CLIENT) {
            $notification->type = 'booking_canceled_by_client';
            $notification->receiver_id = $booking->practitioner_id;
        } else {
            $notification->type = 'booking_canceled_by_practitioner';
            $notification->receiver_id = $booking->user_id;
        }

        $notification->client_id = $booking->user_id;
        $notification->practitioner_id = $booking->practitioner_id;
        $notification->title = $booking->schedule->service->title . ' ' . $booking->schedule->title;
        $notification->old_address = $booking->schedule->location_displayed;
        $notification->datetime_from = $booking->datetime_from;
        $notification->datetime_to = $booking->datetime_to;
        $notification->price_id = $booking->price_id;
        $notification->price_refunded = $refundValue;

        $notification->save();

        if ($actionRole === User::ACCOUNT_CLIENT) {
            event(new BookingCancelledByClient($booking, $cancellation));
        } else {
            event(new BookingCancelledByPractitioner($booking));
        }

        return response(null, 204);
    }


    /**
     * @param \App\Models\Booking $booking
     * @param bool $declineRescheduleRequest
     * @return bool
     */
    private function clientRefund(Booking $booking, bool $declineRescheduleRequest): bool {
        if ($declineRescheduleRequest === true) {
            return $booking->cost;
        }

        if ($booking->datetime_from) {
            $bookingDate = Carbon::parse($booking->datetime_from);
            $now = Carbon::now();
            $diffValue = $booking->schedule->service === 'appointment'
                ? $now->diffInHours($bookingDate)
                : $now->diffInDays($bookingDate);
            if ($bookingDate < $now && $diffValue > $booking->schedule->refund_terms) {
                return $booking->cost;
            }
        }

        return 0;
    }

    private function refundInstallment($subscription_id)
    {
        $invoices = $this->stripe->invoices->all([
            'subscription' => $subscription_id,
            'status' => 'paid'
        ]);

        foreach ($invoices as $invoice){
            //pi_1IvQI2JM28CvbfqX8gp9BiJG
            $this->stripe->refunds->create([
                'payment_intent' => $invoice->paymentIntent
            ]);
        }

        return $invoices;
    }
}
