<?php

namespace App\Actions\Stripe;

use App\Models\Plan;
use App\Models\PractitionerCommission;
use App\Models\Promotion;
use App\Models\Transfer;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;


class TransferFundsWithCommissions {

    public function execute($cost, $practitioner, $schedule = null, $client, $purchase) {
        if ($practitioner->plan instanceof Plan) {
            $stripe = app()->make(StripeClient::class);

            $practitionerPlan = $practitioner->plan->commission_on_sale;

            $practitionerCommissions =
                PractitionerCommission::where('practitioner_id', $practitioner->id)->where(function($q) {
                    $q->where('is_dateless', true)->orWhereRaw('date_from <= NOW() AND date_to >= NOW()');
                })->get();

            // transfer value depends of DiscountType
            if ($purchase->discount > 0 && $purchase->discount_applied === Promotion::APPLIED_HOST) {
                $cost += $purchase->discount;
            }

            $reductions[] = $cost * $practitionerPlan / 100;

            foreach ($practitionerCommissions as $practitionerCommission) {
                $reductions[] = $cost * $practitionerCommission->rate / 100;
            }

            $amount = $cost;
            foreach ($reductions as $reduction) {
                $amount -= $reduction;
            }

            $reference = implode(', ', $purchase->bookings->pluck('reference')->toArray());

            $stripe->transfers->create([
                                           'amount'      => $amount * 100,
                                           'currency'    => config('app.platform_currency'),
                                           'destination' => $practitioner->stripe_account_id,
                                           'metadata'    => [
                                               'Practitioner business email'       => $practitioner->business_email,
                                               'Practitioner busines name'         => $practitioner->business_name,
                                               'Practitioner stripe id'            => $practitioner->stripe_customer_id,
                                               'Practitioner connected account id' => $practitioner->stripe_account_id,
                                               'Client first name'                 => $client->first_name,
                                               'Client last name'                  => $client->last_name,
                                               'Client stripe id'                  => $client->stripe_customer_id,
                                               'Booking reference'                 => $reference
                                           ]
                                       ]);

            $transfer = new Transfer();
            $transfer->user_id = $practitioner->id;
            $transfer->stripe_account_id = $practitioner->stripe_account_id;
            $transfer->status = 'success';
            $transfer->amount = $amount;
            $transfer->amount_original = $purchase->price;
            $transfer->currency = config('app.platform_currency');
            $transfer->schedule_id = $schedule->id ?? null;
            $transfer->description = 'transfer for a schedule purchase';
            $transfer->save();

            Log::channel('practitioner_commissions_success')->info('Commission transfer success:', [
                'user_id'           => $practitioner->id,
                'plan_id'           => $practitioner->plan_id,
                'stripe_account_id' => $practitioner->stripe_account_id,
                'amount'            => $amount,
                'amount_original'   => $cost,
                'schedule_id'       => $schedule->id ?? null,
            ]);

            return $transfer;
        }

        Log::channel('practitioner_commissions_error')
           ->info('Unable to transfer funds to the practitioner. Empty Plan:', [
               'user_id'           => $practitioner->id,
               'plan_id'           => $practitioner->plan_id,
               'stripe_account_id' => $practitioner->stripe_account_id,
           ]);
    }
}
