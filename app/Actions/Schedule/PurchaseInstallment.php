<?php


namespace App\Actions\Schedule;

use App\Models\Price;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;

class PurchaseInstallment
{
    public $customer;
    /**
     * @var Schedule
     */
    public $schedule;
    /**
     * @var Service
     */
    public $service;

    /**
     * @var StripeClient
     */
    protected $stripe;

    public function execute(Price $price, $request, $paymentMethodId, $installments = 1, $cost)
    {
        $this->customer = $request->user();
        $this->stripe   = app()->make(StripeClient::class);
        $this->schedule = $price->schedule;
        $this->service  = $this->schedule->service;

        $deposit     = $this->chargeDeposit($paymentMethodId);
        $subscription = $this->chargeInstallment($paymentMethodId, $installments, $cost);

        return $subscription;
    }

    protected function chargeDeposit($paymentMethodId): \Stripe\PaymentIntent
    {
        $depositAmount = $this->schedule->deposit_amount;

        $paymentIntent = $this->stripe->paymentIntents->create([
            'amount'               => $depositAmount * 100,
            'currency'             => config('app.platform_currency'),
            'payment_method_types' => ['card'],
            'customer'             => Auth::user()->stripe_customer_id,
            'payment_method'       => $paymentMethodId
        ]);

        $paymentIntent = $this->stripe->paymentIntents->confirm($paymentIntent->id, [
            'payment_method' => $paymentMethodId
        ]);

        return $paymentIntent;
    }

    protected function chargeInstallment($paymentMethodId, $installments = 1, $cost): ?\Stripe\Subscription
    {
        $depositAmount = $this->schedule->deposit_amount;
        $finalCost     = $cost - $depositAmount;

        $dateFrom = Carbon::now();
        $dateTo   = Carbon::parse($this->schedule->deposit_final_date);

        $daysTillFinalDate    = $dateFrom->diffInDays($dateTo) ?? 1;
        $chargeEvery          = (int)($daysTillFinalDate / $installments);
        $chargeAmount         = $finalCost / $installments * 100;
        $finalInstallmentDate = Carbon::now()->addDays($chargeEvery * $installments);

        if ($finalCost > 0) {
            $stripePrice  = $this->stripe->prices->create([
                'currency'    => config('app.platform_currency'),
                'product'     => $this->service->stripe_id,
                'unit_amount' => $chargeAmount,
                'recurring'   => ['interval' => 'day', 'interval_count' => $chargeEvery,],
            ]);
            $subscription = $this->stripe->subscriptions->create([
                'default_payment_method' => $paymentMethodId,
                'customer'               => $this->customer->stripe_customer_id,
                'cancel_at'              => $finalInstallmentDate->timestamp,
                'items'                  => [
                    ['price' => $stripePrice->id],
                ],
            ]);

            return $subscription;
        }
    }
}
