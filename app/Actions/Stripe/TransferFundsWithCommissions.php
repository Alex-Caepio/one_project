<?php

namespace App\Actions\Stripe;

use App\Models\Plan;
use App\Models\Promotion;
use App\Models\Transfer;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class TransferFundsWithCommissions
{
    public function execute($cost, $practitioner, $schedule, $client, $purchase, $chargeId = null, $booking = null): ?Transfer
    {
        if (!$practitioner->plan instanceof Plan) {
            Log::channel('practitioner_commissions_error')
                ->info('Unable to transfer funds to the practitioner. Empty Plan:', [
                    'user_id' => $practitioner->id,
                    'plan_id' => $practitioner->plan_id,
                    'stripe_account_id' => $practitioner->stripe_account_id,
                ]);

            return null;
        }
        $stripe = app()->make(StripeClient::class);

        // define if commission is overridden in admin panel
        $practitionerCommissions = $practitioner->getCommission();

        $request['source_transaction'] = $chargeId;

        // transfer value depends of DiscountType
        if ($purchase->discount > 0 && $purchase->discount_applied === Promotion::APPLIED_HOST) {
            $cost += $purchase->discount;
            unset($request['source_transaction']);
        }

        $amount = $cost - $cost * $practitionerCommissions / 100;

        if (!empty($booking)) {
            $reference = $booking->reference;
        } else {
            $reference = implode(', ', $purchase->bookings->pluck('reference')->toArray());
        }

        if (!empty($purchase->promocode)) {
            $applied_to = $purchase->promocode->promotion->applied_to;
        } else {
            $applied_to = '';
        }

        /*
         *      When we should transfer to practitioner less than was paid by client
         *   important to point SOURCE_TRANSACTION, because in case of negative balance
         *   transfer to practitioner will be rejected by stripe
         *      When client used promocode transfer amount to practitioner will be more
         *   than client paid. In that case we could not use SOURCE_TRANSACTION.
         */
        $request = array_merge($request, [
            'amount' => intval(round($amount * 100, 0, PHP_ROUND_HALF_DOWN)),
            'currency' => config('app.platform_currency'),
            'destination' => $practitioner->stripe_account_id,
            'description' => 'New booking transfer to practitioner',
            // https://stripe.com/docs/connect/charges-transfers#transfer-availability
            'metadata' => [
                'Practitioner business email' => $practitioner->business_email,
                'Practitioner business name' => $practitioner->business_name,
                'Practitioner stripe id' => $practitioner->stripe_customer_id,
                'Practitioner connected account id' => $practitioner->stripe_account_id,
                'Tom Commission' => $practitioner->getCommission() . '%',
                'Application Fee' =>
                    round($purchase->price * $practitioner->getCommission() / 100, 2, PHP_ROUND_HALF_DOWN)
                    . '(' . config('app.platform_currency') . ')',
                'Client first name' => $client->first_name,
                'Client last name' => $client->last_name,
                'Client stripe id' => $client->stripe_customer_id,
                'Booking reference' => $reference,
                'Promoted by' => $applied_to,
                'Charge id' => $chargeId,
            ]
        ]);

        $stripeTransfer = $stripe->transfers->create($request);

        $transfer = new Transfer();
        $transfer->user_id = $practitioner->id;
        $transfer->stripe_account_id = $practitioner->stripe_account_id;
        $transfer->stripe_transfer_id = $stripeTransfer->id;
        $transfer->status = 'success';
        $transfer->amount = round($amount, 2, PHP_ROUND_HALF_DOWN);
        $transfer->amount_original = $purchase->price;
        $transfer->currency = config('app.platform_currency');
        $transfer->schedule_id = $schedule->id ?? null;
        $transfer->purchase_id = $purchase->id ?? null;
        $transfer->description = 'transfer for a schedule purchase';
        $transfer->save();

        Log::channel('practitioner_commissions_success')
            ->info('Commission transfer success:', [
                'user_id' => $practitioner->id,
                'plan_id' => $practitioner->plan_id,
                'stripe_account_id' => $practitioner->stripe_account_id,
                'amount' => round($amount, 2, PHP_ROUND_HALF_DOWN),
                'amount_original' => $cost,
                'schedule_id' => $schedule->id ?? null,
            ]);

        return $transfer;
    }
}
