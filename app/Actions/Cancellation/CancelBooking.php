<?php

namespace App\Actions\Cancellation;

use App\Events\BookingCancelledByPractitioner;
use App\Events\BookingCancelledByClient;
use App\Events\ContractualServiceUpdateDeclinedBookingCancelled;
use App\Models\Booking;
use App\Models\Cancellation;
use App\Models\Notification;
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
        bool $declineRescheduleRequest = false,
        ?string $roleFromRequest = null,
        ?bool $cancelledByPractitioner = false
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

                if (!$refundData['isFullRefund']) {
                    $stripeRefundData['amount'] = $refundData['refundSmallestUnit'];
                }

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
        }

        if ($refundData['practitionerCharge'] > 0 && $booking->practitioner->stripe_account_id) {
            $this->reverseTransferToPractitioner($refundData, $booking);
        }

        if ($booking->is_installment) {
            $this->refundInstallment($booking->purchase->subscription_id);
            $this->stripe->subscriptions->cancel($booking->purchase->subscription_id);
        }

        $booking->cancelled_at = Carbon::now();
        $booking->status = 'canceled';
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
            $notificationType = 'booking_canceled_by_practitioner';
            $notification->receiver_id = $booking->user_id;
        } else {
            if ($actionRole === User::ACCOUNT_CLIENT) {
                $notificationType = $isAmendment ? 'amendment_canceled_by_client' : 'booking_canceled_by_client';
                $notification->receiver_id = $booking->practitioner_id;
            } else {
                $notificationType = 'booking_canceled_by_practitioner';
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
        $notification->price_refunded = $refundData['refundTotal'];
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
        $invoices = $this->stripe->invoices->all([
            'subscription' => $subscription_id,
            'status' => 'paid'
        ]);

        foreach ($invoices as $invoice) {
            if (!is_null($invoice->paymentIntent)) {
                $this->stripe->refunds->create([
                    'payment_intent' => $invoice->paymentIntent
                ]);
            }
        }
    }

    private function calculateRefundValue(
        string $actionRole,
        Booking $booking,
        bool $declineRescheduleRequest,
        bool $cancelledByPractitioner
    ): array {
        $stripeFee = (int) config('app.platform_cancellation_fee'); // 3%
        $planRate = $booking->practitioner->getCommission();
        $paid = $booking->is_installment ? $booking->purchase->deposit_amount : $booking->cost;

        $stripeFeeAmount = $paid * $stripeFee / 100;
        $planRateAmount = $paid * $planRate / 100;

        $result = [
            'isFullRefund' => false,
            'refundTotal' => 0,
            'refundSmallestUnit' => 0,
            'bookingCost' => $paid,
            'stripeFee' => $stripeFee,
            'stripeFeeAmount' => $stripeFeeAmount,
            'planRate' => $planRate,
            'planRateAmount' => $planRateAmount,
            'practitionerCharge' => 0,
        ];

        // What is the difference with cancellation by practitioner?
        /*if ($actionRole === User::ACCOUNT_PRACTITIONER || $declineRescheduleRequest === true) {
            $result = $this->cancelledWithDeclineRescheduleRequest($result);
        } else {
            if ($cancelledByPractitioner) {
                $result = $this->cancelledByPractitioner($result);
            } else { // cancelled by client
                $result = $this->cancelledByClient($result, $booking);
            }
        }*/
        if ($cancelledByPractitioner) {
            $result = $this->cancelledByPractitioner($result);
        } else { // cancelled by client
            $result = $this->cancelledByClient($result, $booking);
        }

        $result['refundSmallestUnit'] = (int)($result['refundTotal'] * 100);

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

    private function cancelledByPractitioner(array $result): array
    {
        $result['isFullRefund'] = true;
        $result['refundTotal'] = $result['bookingCost'];
        $result['practitionerCharge'] = $result['bookingCost'] - $result['planRateAmount'] + $result['stripeFeeAmount'];

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
            || (!$isDateless && $bookingDate > $now && $diffValue > $booking->schedule->refund_terms);

        if ($isRefundAllowed) {
            $result['refundTotal'] = $result['bookingCost'] - $result['stripeFeeAmount'];
            $result['practitionerCharge'] = $result['bookingCost'] - $result['planRateAmount'];
        } else {
            $result['refundTotal'] = 0;
            $result['practitionerCharge'] = 0;
        }

        return $result;
    }

    private function reverseTransferToPractitioner(array $refundData, Booking $booking)
    {
        $transfer = Transfer::where('purchase_id', $booking->purchase->id)->first();
        if (empty($transfer)) {
            return;
        }

        try {
            $this->stripe->transfers->createReversal(
                $transfer->stripe_transfer_id,
                [
                    'amount' => $refundData['practitionerCharge'] * 100,
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
