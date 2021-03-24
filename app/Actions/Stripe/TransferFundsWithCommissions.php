<?php

namespace App\Actions\Stripe;

use App\Models\PractitionerCommission;
use App\Models\Transfer;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;


class TransferFundsWithCommissions {
    public function execute($cost, $practitoner, $schedule = null) {
        $stripe = app()->make(StripeClient::class);

        $practitionerPlan = $practitoner->plan->commission_on_sale;

        $practitionerCommissions =
            PractitionerCommission::where('practitioner_id', $practitoner->id)->where(function($q) {
                    $q->where('is_dateless', true)->orWhereRaw('date_from <= NOW() AND date_to >= NOW()');
                })->get();

        $reductions[] = $cost * $practitionerPlan / 100;

        foreach ($practitionerCommissions as $practitionerCommission) {
            $reductions[] = $cost * $practitionerCommission->rate / 100;
        }

        $amount = $cost;
        foreach ($reductions as $reduction) {
            $amount -= $reduction;
        }

        $stripe->transfers->create([
                                       'amount'      => $amount,
                                       'currency'    => config('app.platform_currency'),
                                       'destination' => $practitoner->stripe_account_id,
                                   ]);

        $transfer = new Transfer();
        $transfer->user_id = $practitoner->id;
        $transfer->stripe_account_id = $practitoner->stripe_account_id;
        $transfer->status = 'success';
        $transfer->amount = $amount;
        $transfer->amount_original = $cost;
        $transfer->currency = config('app.platform_currency');
        $transfer->schedule_id = $schedule->id ?? null;
        $transfer->description = 'transfer for a schedule purchase';
        $transfer->save();


        return $transfer;
    }
}
