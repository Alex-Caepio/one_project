<?php

namespace App\Actions\Stripe;

use App\Models\PractitionerCommission;
use App\Models\Transfer;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;


class TransferFundsWithCommissions {
    public function execute($cost, $practitoner, $schedule = null, $client, $purchase) {
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

        $refference = implode(', ', $purchase->bookings->pluck('reference')->toArray());

        $stripe->transfers->create([
            'amount'      => $amount * 100,
            'currency'    => config('app.platform_currency'),
            'destination' => $practitoner->stripe_account_id,
            'metadata'    => [
                'Practitioner business email'       => $practitoner->business_email,
                'Practitioner busines name'         => $practitoner->business_name,
                'Practitioner stripe id'            => $practitoner->stripe_customer_id,
                'Practitioner connected account id' => $practitoner->stripe_account_id,
                'Client first name'                 => $client->first_name,
                'Client last name'                  => $client->last_name,
                'Client stripe id'                  => $client->stripe_customer_id,
                'Booking refference'                => $refference
            ]
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
