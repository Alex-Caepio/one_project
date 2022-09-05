<?php

namespace App\Actions\Cancellation;

use App\Events\BookingCancelledByPractitioner;
use App\Events\BookingCancelledByClient;
use App\Events\ContractualServiceUpdateDeclinedBookingCancelled;
use App\Models\Booking;
use App\Models\Cancellation;
use App\Models\Instalment;
use App\Models\Promotion;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

/**
 * https://stripe.com/docs/connect/account-debits#charging-a-connected-account
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

        $actionRole = $this->getActionRole($roleFromRequest, $booking, Auth::id());

        $booking->load(['user', 'practitioner', 'purchase', 'schedule', 'schedule.service']);
        if (!$booking->purchase || !$booking->practitioner || !$booking->user) {
            throw new Exception('Incorrect model relation in booking #' . $booking->id);
        }

        $stripeRefund = null;

        $refundData = $this->calculateRefundValue(
            $cancelledByPractitioner ? User::ACCOUNT_PRACTITIONER : $actionRole,
            $booking,
            $declineRescheduleRequest
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

        if ($refundData['refundTotal'] > 0) {
            try {
                $paymentIntent = $this->stripe->paymentIntents->retrieve($booking->purchase->stripe_id);

                $stripeRefundData = ['payment_intent' => $paymentIntent->id];
                $stripeRefundData['amount'] = $refundData['refundSmallestUnit'];

                $stripeRefund = $this->stripe->refunds->create($stripeRefundData);
                Log::channel('stripe_refund')
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
                Log::channel('stripe_refund')
                    ->error('Stripe refund error: ', [
                        'user_id' => $booking->user_id ?? null,
                        'practitioner_id' => $booking->practitioner_id ?? null,
                        'booking_id' => $booking->id ?? null,
                        'payment_stripe' => $booking->purchase->stripe_id ?? null,
                        'message' => $e->getMessage(),
                    ]);
            }

            if ($booking->is_installment) {
                run_action(RefundInstalments::class, $booking->purchase->subscription_id);
                run_action(CancelSubscription::class, $booking->purchase->subscription_id);
            }

            if ($refundData['practitionerCharge'] > 0 && $booking->practitioner->stripe_account_id) {
                run_action(ReverseTransferOfPractitioner::class, $refundData, $booking);
            }
        } elseif ($booking->is_installment) {
            // Cancels the subscription without any paids.
            run_action(CancelSubscription::class, $booking->purchase->subscription_id);
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

        run_action(NotifyUser::class, $actionRole, $booking, $refundData);

        if ($actionRole === User::ACCOUNT_PRACTITIONER) {
            event(new BookingCancelledByPractitioner($booking));
        } elseif ($declineRescheduleRequest) {
            event(new ContractualServiceUpdateDeclinedBookingCancelled($booking));
        } elseif ($actionRole === User::ACCOUNT_CLIENT) {
            event(new BookingCancelledByClient($booking, $cancellation));
        } else {
            event(new BookingCancelledByPractitioner($booking));
        }

        return response(null, 204);
    }

    private function getActionRole(?string $requestRole, Booking $booking, int $currentUserId): string
    {
        if ($requestRole && in_array($requestRole, User::getAccountTypes(), true)) {
            return $requestRole;
        }

        return $currentUserId === $booking->user_id ? User::ACCOUNT_CLIENT : User::ACCOUNT_PRACTITIONER;
    }

    private function calculateRefundValue(string  $actionRole, Booking $booking, bool $declineRescheduleRequest): array
    {
        $cancelledByPractitioner = $actionRole === User::ACCOUNT_PRACTITIONER;
        $stripeFee = (int) config('app.platform_cancellation_fee'); // 3%
        $planRate = $booking->practitioner->getCommission();
        $paid = $booking->is_installment
            ? $booking->purchase->amount * $booking->purchase->deposit_amount
            : $booking->cost
        ;

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
        } else {
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

        if ($this->isRefundAllowed($booking)) {
            $result['refundTotal'] = $result['bookingCost'] - $result['stripeFeeAmount'];
            $result['practitionerCharge'] = $result['bookingCost'] - $result['planRateAmount'];
        } else {
            $result['refundTotal'] = 0;
            $result['installmentRefund'] = 0;
            $result['practitionerCharge'] = 0;
        }

        return $result;
    }

    private function isRefundAllowed(Booking $booking): bool
    {
        $isDateless = $booking->schedule->service->isDateless();
        $bookingDate = $isDateless ? $booking->created_at : $booking->datetime_from;
        $now = Carbon::now();
        $diffValue = $booking->schedule->service->service_type_id === Service::TYPE_APPOINTMENT
            ? $now->diffInHours($bookingDate)
            : $now->diffInDays($bookingDate)
        ;

        // Bespoke.
        // Terms = 0 is without refund.
        // Terms > 0 is refund can be done during <trms> days after begin of booking.
        if ($isDateless) {
            return $booking->refund_terms !== 0
                && $now->greaterThanOrEqualTo($bookingDate)
                && $diffValue <= $booking->refund_terms
            ;
        }

        return $bookingDate->greaterThan($now)
            && ($booking->refund_terms === 0 || $diffValue >= $booking->refund_terms)
        ;
    }
}
