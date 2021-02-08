<?php

namespace App\Actions\Stripe;

use App\Models\PractitionerCommission;
use Stripe\StripeClient;


class TransferFundsWithCommissions
{
    public function execute($cost, $practitoner)
    {
        $stripe = app()->make(StripeClient::class);

        $practitionerPlan = $practitoner->plan->commission_on_sale;

        $practitionerCommissions = PractitionerCommission::where('practitioner_id', $practitoner->id)
            ->where(function ($q) {
                $q->where('is_dateless', true)
                ->orWhereRaw('date_from <= NOW() AND date_to >= NOW()');
            })->get();

        $reductions[] = $cost * $practitionerPlan / 100;

        foreach ($practitionerCommissions as $practitionerCommission){
            $reductions[] = $cost * $practitionerCommission->rate / 100;
        }

        $amount = $cost;
        foreach ($reductions as $reduction){
            $amount -= $reduction;
        }

        $stripe->transfers->create([
            'amount'      => $amount,
            'currency'    => 'usd',
            'destination' => $practitoner->stripe_account_id,
        ]);
    }
}
