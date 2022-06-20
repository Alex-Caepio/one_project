<?php

namespace App\Actions\Cancellation;

use App\Events\BookingCancelledByPractitioner;
use App\Events\BookingCancelledByClient;
use App\Events\ContractualServiceUpdateDeclinedBookingCancelled;
use App\Models\Booking;
use App\Models\Cancellation;
use App\Models\Instalment;
use App\Models\Notification;
use App\Models\Promotion;
use App\Models\Purchase;
use App\Models\RescheduleRequest;
use App\Models\Service;
use App\Models\Transfer;
use App\Models\User;
use Carbon\Carbon;
use Exception;
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
class CancelBooking
{
    private StripeClient $stripe;

    public function __construct(StripeClient $stripe)
    {
        $this->stripe = $stripe;
    }

    public function execute(
        Booking $booking,
        bool    $declineRescheduleRequest = false,
        ?string $roleFromRequest = null,
        ?bool   $cancelledByPractitioner = false
    ) {
        if (!$booking->isActive()) {
            return;
        }

        $booking->load(['user', 'practitioner', 'purchase', 'schedule', 'schedule.service']);
        if (!$booking->purchase || !$booking->practitioner || !$booking->user) {
            throw new Exception('Incorrect model relation in booking #' . $booking->id);
        }

        $stripeRefund = null;

        if ($roleFromRequest !== null) {
            $actionRole = $roleFromRequest;
        } else {
            $actionRole = Auth::id() === $booking->user_id ? User::ACCOUNT_CLIENT : User::ACCOUNT_PRACTITIONER;
        }

        if ($actionRole === User::ACCOUNT_PRACTITIONER) {
            $cancelledByPractitioner = true;
        }

        $refundData = $this->calculateRefundValue(
            $actionRole,
            $booking,
            $declineRescheduleRequest,
            $cancelledByPractitioner
        );

        Log::channel('stripe_refund_info')
            ->info('Stripe refund info: ', [
                'user_id' => $booking->user_id,
                'practitioner_id' => $booking->practitioner_id,
                'booking_id' => $booking->id,
                'booking_cost' => $booking->cost,
                'refund_amount' => $refundData['refundTotal'],
                'refund_smallunits_amount' => $refundData['refundSmallestUnit'],
                'charge_amount' => $refundData['practitionerCharge'],
                'payment_stripe' => $booking->purchase->stripe_id,
                'action_role' => $actionRole,
                'is_decline' => $declineRescheduleRequest,
            ]);

        $rescheduleRequest = RescheduleRequest::where('booking_id', $booking->id)->first();

        if ($rescheduleRequest) {
            $isAmendment = $rescheduleRequest->isAmendment();
            $rescheduleRequest->delete();
        } else {
            $isAmendment = false;
        }

        if ($refundData['refundTotal'] > 0) {
            try {
                $paymentIntent = $this->stripe->paymentIntents->retrieve($booking->purchase->stripe_id);

                $stripeRefundData = ['payment_intent' => $paymentIntent->id];
                $stripeRefundData['amount'] = $refundData['refundSmallestUnit'];

                $stripeRefund = $this->stripe->refunds->create($stripeRefundData);
                Log::channel('stripe_refund_success')
                    ->info('Stripe refund success: ', [
                        'user_id' => $booking->user_id ?? null,
                        'practitioner_id' => $booking->practitioner_id ?? null,
                        'booking_id' => $booking->id ?? null,
                        'refund_amount' => $refundData['refundTotal'],
                        'charge_amount' => $refundData['practitionerCharge'],
                        'payment_stripe' => $booking->purchase->stripe_id ?? null,
                        'refund_stripe_id' => $stripeRefund->id,
                        'statement_descriptor_suffix' => $booking->reference
                    ]);
            } catch (ApiErrorException $e) {
                Log::channel('stripe_refund_fail')
                    ->info('Stripe refund error: ', [
                        'user_id' => $booking->user_id ?? null,
                        'practitioner_id' => $booking->practitioner_id ?? null,
                        'booking_id' => $booking->id ?? null,
                        'payment_stripe' => $booking->purchase->stripe_id ?? null,
                        'message' => $e->getMessage(),
                    ]);
            }

            if ($booking->is_installment) {
                $this->refundInstallment($booking->purchase->subscription_id);
                try {
                    $this->stripe->subscriptions->cancel($booking->purchase->subscription_id);
                } catch (Exception $e) {
                    Log::channel('stripe_refund_fail')
                        ->info('Subscription cancel fail: ', [
                            'subscription_id' => $booking->purchase->subscription_id,
                            'message' => $e->getMessage(),
                        ]);
                }
            }

            if ($refundData['practitionerCharge'] > 0 && $booking->practitioner->stripe_account_id) {
                $this->reverseTransferToPractitioner($refundData, $booking);
            }
        }

        $booking->cancelled_at = Carbon::now();
        $booking->status = Booking::CANCELED_STATUS;
        $booking->save();

        $cancellation = new Cancellation();
        $cancellation->fill([
            'user_id' => $booking->user_id,
            'booking_id' => $booking->id,
            'purchase_id' => $booking->purchase_id,
            'practitioner_id' => $booking->practitioner_id,
            'amount' => $refundData['refundTotal'],
            'fee' => $refundData['practitionerCharge'],
            'cancelled_by_client' => $actionRole === User::ACCOUNT_CLIENT,
            'stripe_id' => $stripeRefund->id ?? null
        ]);
        $cancellation->save();

        $notification = new Notification();

        if ($cancelledByPractitioner) {
            $notificationType = Notification::BOOKING_CANCELED_BY_PRACTITIONER;
            $notification->receiver_id = $booking->user_id;
        } else {
            if ($actionRole === User::ACCOUNT_CLIENT) {
                $notificationType = $isAmendment
                    ? Notification::AMENDMENT_CANCELED_BY_PRACTITIONER
                    : Notification::BOOKING_CANCELED_BY_CLIENT;
                $notification->receiver_id = $booking->practitioner_id;
            } else {
                $notificationType = Notification::BOOKING_CANCELED_BY_PRACTITIONER;
                $notification->receiver_id = $booking->user_id;
            }
        }

        $notification->type = $notificationType;
        $notification->client_id = $booking->user_id;
        $notification->practitioner_id = $booking->practitioner_id;
        $notification->booking_id = $booking->id;
        $notification->title = $booking->schedule->service->title . ' ' . $booking->schedule->title;

        if ($rescheduleRequest) {
            $notification->old_address = $rescheduleRequest->old_location_displayed;
            $notification->new_address = $rescheduleRequest->new_location_displayed;
            $notification->old_datetime = $rescheduleRequest->old_start_date;
            $notification->new_datetime = $rescheduleRequest->new_start_date;
        }

        $notification->service_id = $booking->schedule->service_id;
        $notification->datetime_from = $booking->datetime_from;
        $notification->datetime_to = $booking->datetime_to;
        $notification->price_id = $booking->price_id;
        $notification->price_refunded = $refundData['refundTotal'] > 0 && $booking->is_installment ? $refundData['installmentRefund'] : $refundData['refundTotal'];
        $notification->price_payed = $booking->cost;

        $notification->save();

        if ($cancelledByPractitioner) {
            event(new BookingCancelledByPractitioner($booking));
        } else {
            if ($declineRescheduleRequest) {
                event(new ContractualServiceUpdateDeclinedBookingCancelled($booking));
            } else {
                if ($actionRole === User::ACCOUNT_CLIENT) {
                    event(new BookingCancelledByClient($booking, $cancellation));
                } else {
                    event(new BookingCancelledByPractitioner($booking));
                }
            }
        }

        return response(null, 204);
    }

    private function refundInstallment($subscription_id): void
    {
        // refund to holistify stripe account
        $purchase = Purchase::where('subscription_id', $subscription_id)->first();
        $transfers = $purchase->transfer()->where('is_installment', true)->whereNull('stripe_transfer_reversal_id')->get();

        try {
            foreach ($transfers as $transfer) {
                $result = $this->stripe->transfers->createReversal($transfer->stripe_transfer_id);
                $transfer->stripe_transfer_reversal_id = $result->id;
                $transfer->save();

                Log::channel('stripe_refund_success')
                    ->info('Reversal transfer success: ', [
                        'Parent transfer id' => $transfer->stripe_transfer_id,
                    ]);
            }
        } catch (Exception $e) {
            Log::channel('stripe_refund_fail')
                ->info('Reversal transfer failed: ', [
                    'source_transfer_id' => $transfer->stripe_transfer_id,
                    'message' => $e->getMessage(),
                ]);
            return;
        }

        try {
            $stripeFee = (int)config('app.platform_cancellation_fee'); // 3%
            // then refund to user
            $invoices = $this->stripe->invoices->all([
                'subscription' => $subscription_id,
                'status' => 'paid',
            ]);

            foreach ($invoices as $invoice) {
                if (!is_null($invoice['payment_intent'])) {
                    try {
                        $result = $this->stripe->refunds->create([
                            'payment_intent' => $invoice['payment_intent'],
                            'amount' => intval($invoice['amount_paid'] - round($invoice['amount_paid'] / 100 * $stripeFee, 0, PHP_ROUND_HALF_DOWN)),
                            'reverse_transfer' => false,
                        ]);

                        Log::channel('stripe_refund_success')
                            ->info('Payment intent refund result: ', [
                                'refund' => $result,
                            ]);
                    } catch (Exception $e) {
                        Log::channel('stripe_refund_fail')
                            ->info('Stripe get subscription invoices error: ', [
                                'payment_intent' => $invoice['payment_intent'],
                                'message' => $e->getMessage(),
                            ]);
                    }
                } else {
                    Log::channel('stripe_refund_fail')
                        ->info('Invoice has no payment intent: ', [
                            'invoice' => $invoice,
                        ]);
                }
            }
        } catch (Exception $e) {
            Log::channel('stripe_refund_fail')
                ->info('Stripe get subscription invoices error: ', [
                    'subscription' => $subscription_id,
                    'message' => $e->getMessage(),
                ]);
        }

        $purchase->cancelled_at_subscription = Carbon::now();
        $purchase->save();
    }

    private function calculateRefundValue(
        string  $actionRole,
        Booking $booking,
        bool    $declineRescheduleRequest,
        bool    $cancelledByPractitioner
    ): array {
        $stripeFee = (int)config('app.platform_cancellation_fee'); // 3%
        $planRate = $booking->practitioner->getCommission();
        $paid = $booking->is_installment ? $booking->purchase->amount * $booking->purchase->deposit_amount : $booking->cost;

        $stripeFeeAmount = round($paid * $stripeFee / 100, 2);
        $planRateAmount = round($paid * $planRate / 100, 2);

        if ($booking->is_installment) {
            $installmentAmount = Instalment::where('purchase_id', $booking->purchase->id)
                ->where('is_paid', 1)
                ->sum('payment_amount');
            $installmentFeeAmount = round($installmentAmount * $stripeFee / 100, 2);
        }


        $result = [
            'isFullRefund' => false,
            'refundTotal' => 0,
            'refundSmallestUnit' => 0,
            'bookingCost' => $paid, // in case of installment it equals to deposit
            'stripeFee' => $stripeFee,
            'stripeFeeAmount' => $stripeFeeAmount,
            'planRate' => $planRate,
            'planRateAmount' => $planRateAmount,
            'practitionerCharge' => 0,
            'installmentAmount' => $installmentAmount ?? 0,
            'installmentFeeAmount' => $installmentFeeAmount ?? 0,
            'installmentRefund' => $booking->is_installment ? $installmentAmount - $installmentFeeAmount : 0,
        ];

        if ($cancelledByPractitioner || $declineRescheduleRequest) {
            $result = $this->cancelledByPractitioner($result, $booking);
        } else { // cancelled by client
            $result = $this->cancelledByClient($result, $booking);
        }


        $result['refundSmallestUnit'] = intval(sprintf("%.0f", $result['refundTotal'] * 100));

        return $result;
    }

    private function cancelledWithDeclineRescheduleRequest(array $result)
    {
        // How can it be a full refund if a plan rate amount is subtracted?
        $result['isFullRefund'] = true;
        $result['refundTotal'] = $result['bookingCost'] - $result['planRateAmount'];
        $result['practitionerCharge'] = 0;

        return $result;
    }

    private function cancelledByPractitioner(array $result, Booking $booking): array
    {
        $result['isFullRefund'] = false;
        $result['refundTotal'] = $result['bookingCost'] - $result['stripeFeeAmount'];
        $result['practitionerCharge'] = $result['bookingCost'] - $result['planRateAmount'];

        if ($booking->purchase->discount_applied === Promotion::APPLIED_HOST) {
            $result['practitionerCharge'] = round(($result['bookingCost'] + $booking->purchase->discount) * (100 - $result['planRate']) / 100, 2, PHP_ROUND_HALF_DOWN);
        } else if ($booking->purchase->discount_applied === Promotion::APPLIED_BOTH) {
            $result['practitionerCharge'] = round($result['bookingCost'] * (100 - $result['planRate']) / 100, 2, PHP_ROUND_HALF_DOWN);
        }

        return $result;
    }

    private function cancelledByClient(array $result, Booking $booking): array
    {
        $result['isFullRefund'] = false;

        $isDateless = $booking->schedule->service->isDateless();
        $bookingDate = $isDateless
            ? Carbon::parse($booking->created_at)
            : Carbon::parse($booking->datetime_from);
        $now = Carbon::now();
        $diffValue = $booking->schedule->service->service_type_id === Service::TYPE_APPOINTMENT
            ? $now->diffInHours($bookingDate)
            : $now->diffInDays($bookingDate);
        $isRefundAllowed = ($isDateless && $now >= $bookingDate && $diffValue < $booking->schedule->refund_terms)
            || (!$isDateless && $bookingDate > $now && ($booking->schedule->refund_terms == 0 || $diffValue > $booking->schedule->refund_terms));

        if ($isRefundAllowed) {
            $result['refundTotal'] = $result['bookingCost'] - $result['stripeFeeAmount'];
            $result['practitionerCharge'] = $result['bookingCost'] - $result['planRateAmount'];
        } else {
            $result['refundTotal'] = 0;
            $result['installmentRefund'] = 0;
            $result['practitionerCharge'] = 0;
        }

        return $result;
    }

    private function reverseTransferToPractitioner(array $refundData, Booking $booking)
    {
        $transfer = Transfer::where('purchase_id', $booking->purchase->id)
            ->where('is_installment', 0)
            ->whereNull('stripe_transfer_reversal_id')
            ->first();

        if (empty($transfer)) {
            return;
        }

        // For multi appointment
        if (count($booking->purchase->bookings)) {
            $refundAmount = $refundData['practitionerCharge'];
        } else {
            $refundAmount = $transfer->amount;
        }

        $refundAmount = intval(round($refundAmount * 100, 0, PHP_ROUND_HALF_DOWN));

        try {
            $this->stripe->transfers->createReversal(
                $transfer->stripe_transfer_id,
                [
                    'amount' => $refundAmount,
                    'description' => 'Booking cancelled',
                    'metadata' => [
                        'Currency' => config('app.platform_currency'),
                        'Practitioner connected account id' => $booking->practitioner->stripe_account_id,
                        'Transfer id' => $transfer->stripe_transfer_id,
                        'Booking reference' => $booking->reference,
                    ]
                ],
            );
        } catch (Exception $e) {
            Log::channel('stripe_refund_fail')
                ->info('Stripe refund error: ', [
                    'user_id' => $booking->user_id ?? null,
                    'practitioner_id' => $booking->practitioner_id ?? null,
                    'charge' => $refundData['practitionerCharge'],
                    'booking_id' => $booking->id ?? null,
                    'payment_stripe' => $booking->purchase->stripe_id ?? null,
                    'transfer_stripe' => $transfer->stripe_transfer_id ?? null,
                    'message' => $e->getMessage(),
                ]);
        }
    }
}
