<?php

namespace App\Actions\Cancellation;

use App\Models\Instalment;
use App\Models\Purchase;
use App\Models\Transfer;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Stripe\Refund;
use Stripe\StripeClient;

class RefundInstalments
{
    private StripeClient $stripe;

    public function __construct(StripeClient $stripe)
    {
        $this->stripe = $stripe;
    }

    public function execute(string $subscriptionId): void
    {
        // refund to holistify stripe account
        $purchase = Purchase::where('subscription_id', $subscriptionId)->first();
        $transfers = $this->getTransfersOfInstalmentsByPurchase($purchase);

        try {
            foreach ($transfers as $transfer) {
                $result = $this->stripe->transfers->createReversal($transfer->stripe_transfer_id);
                $transfer->stripe_transfer_reversal_id = $result->id;
                $transfer->save();

                Log::channel('stripe_refund_success')
                    ->info('Reversal transfer success: ', [
                        'Parent transfer id' => $transfer->stripe_transfer_id,
                    ])
                ;
            }
        } catch (Exception $e) {
            Log::channel('stripe_refund_fail')
                ->error('Reversal transfer failed: ', [
                    'source_transfer_id' => $transfer->stripe_transfer_id,
                    'message' => $e->getMessage(),
                ])
            ;
            return;
        }

        try {
            $stripeFee = (int) config('app.platform_cancellation_fee'); // 3%
            // then refund to user through invoices of the subscription and their payment intents.
            $invoices = $this->stripe->invoices->all([
                'subscription' => $subscriptionId,
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

                        $this->updateInstalmentStatus($invoice['payment_intent'], $result);

                        Log::channel('stripe_refund_success')
                            ->info('Payment intent refund result: ', [
                                'refund' => $result,
                            ])
                        ;
                    } catch (Exception $e) {
                        Log::channel('stripe_refund_fail')
                            ->error('Stripe get subscription invoices error: ', [
                                'payment_intent' => $invoice['payment_intent'],
                                'message' => $e->getMessage(),
                            ])
                        ;
                    }
                } else {
                    Log::channel('stripe_refund_fail')
                        ->warning('Invoice has no payment intent: ', [
                            'invoice' => $invoice,
                        ])
                    ;
                }
            }
        } catch (Exception $e) {
            Log::channel('stripe_refund_fail')
                ->error('Stripe get subscription invoices error: ', [
                    'subscription' => $subscriptionId,
                    'message' => $e->getMessage(),
                ])
            ;
        }

        $purchase->cancelled_at_subscription = Carbon::now();
        $purchase->save();
    }

    /**
     * @return Collection|Transfer[]
     */
    private function getTransfersOfInstalmentsByPurchase(Purchase $purchase): Collection
    {
        return $purchase->transfer()
            ->where('is_installment', true)
            ->whereNull('stripe_transfer_reversal_id')
            ->with('instalment')
            ->get()
        ;
    }

    /**
     * Saves data that instalment has been refunded.
     */
    private function updateInstalmentStatus(string $paymentIndentId, Refund $refund): ?Instalment
    {
        /** @var Instalment|null $instalment */
        $instalment = Instalment::query()
            ->where('stripe_payment_id', $paymentIndentId)
            ->first()
        ;

        if (!$instalment) {
            return null;
        }

        $instalment->stripe_refund_id = $refund->id;
        $instalment->refunded_at = Carbon::parse($refund->created);
        $instalment->save();

        return $instalment;
    }
}
