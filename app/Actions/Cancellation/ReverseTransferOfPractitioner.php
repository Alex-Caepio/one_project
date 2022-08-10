<?php

namespace App\Actions\Cancellation;

use App\Models\Booking;
use App\Models\Transfer;
use Exception;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

/**
 * Refunds transfers of a practitioner to the system (Holistify) in order to return the money to his client.
 */
class ReverseTransferOfPractitioner
{
    private StripeClient $stripe;

    public function __construct(StripeClient $stripe)
    {
        $this->stripe = $stripe;
    }

    public function execute(array $refundData, Booking $booking): void
    {
        /** @var Transfer|null $transfer */
        $transfer = $booking->purchase->transfer()
            ->where('is_installment', 0)
            ->whereNull('stripe_transfer_reversal_id')
            ->first()
        ;

        if (!$transfer) {
            return;
        }

        // For multi appointment
        if (count($booking->purchase->bookings) && !$booking->purchase->discount) {
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
                ->error('Stripe refund error: ', [
                    'user_id' => $booking->user_id ?? null,
                    'practitioner_id' => $booking->practitioner_id ?? null,
                    'charge' => $refundData['practitionerCharge'],
                    'booking_id' => $booking->id ?? null,
                    'payment_stripe' => $booking->purchase->stripe_id ?? null,
                    'transfer_stripe' => $transfer->stripe_transfer_id ?? null,
                    'message' => $e->getMessage(),
                ])
            ;
        }
    }
}
