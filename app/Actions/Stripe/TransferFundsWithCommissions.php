<?php

namespace App\Actions\Stripe;

use App\Models\Plan;
use App\Models\PractitionerCommission;
use App\Models\Promotion;
use App\Models\Transfer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class TransferFundsWithCommissions
{
    public function execute($cost, $practitioner, $schedule = null, $client, $purchase): ?Transfer
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

        $practitionerPlan = $practitioner->plan->commission_on_sale;

        // define if commission is overridden in admin panel
        $practitionerCommissions = PractitionerCommission::query()
            ->where('practitioner_id', $practitioner->id)
            ->where(function (Builder $builder) {
                return $builder
                    ->whereRaw('(is_dateless = 0 AND date_from <= NOW() AND date_to >= NOW())')
                    ->orWhere(function (Builder $builder) {
                        return $builder
                            ->where('is_dateless', '=', 1)
                            ->whereNull('date_from')
                            ->whereNull('date_to');
                    });
            })
            ->min('rate');

        // transfer value depends of DiscountType
        if ($purchase->discount > 0 && $purchase->discount_applied === Promotion::APPLIED_HOST) {
            $cost += $purchase->discount;
        }

        $amount = $cost - $cost * ($practitionerCommissions ?? $practitionerPlan) / 100;

        $reference = implode(', ', $purchase->bookings->pluck('reference')->toArray());

        $stripe->transfers->create([
            'amount' => $amount * 100,
            'currency' => config('app.platform_currency'),
            'destination' => $practitioner->stripe_account_id,
            'metadata' => [
                'Practitioner business email' => $practitioner->business_email,
                'Practitioner business name' => $practitioner->business_name,
                'Practitioner stripe id' => $practitioner->stripe_customer_id,
                'Practitioner connected account id' => $practitioner->stripe_account_id,
                'Client first name' => $client->first_name,
                'Client last name' => $client->last_name,
                'Client stripe id' => $client->stripe_customer_id,
                'Booking reference' => $reference
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
        $transfer->purchase_id = $purchase->id ?? null;
        $transfer->description = 'transfer for a schedule purchase';
        $transfer->save();

        Log::channel('practitioner_commissions_success')
            ->info('Commission transfer success:', [
                'user_id' => $practitioner->id,
                'plan_id' => $practitioner->plan_id,
                'stripe_account_id' => $practitioner->stripe_account_id,
                'amount' => $amount,
                'amount_original' => $cost,
                'schedule_id' => $schedule->id ?? null,
            ]);

        return $transfer;
    }
}
