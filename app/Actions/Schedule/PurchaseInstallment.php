<?php


namespace App\Actions\Schedule;

use App\Http\Requests\Schedule\PurchaseScheduleRequest;
use App\Models\Instalment;
use App\Models\Purchase;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class PurchaseInstallment
{
    public User $customer;
    /**
     * @var Schedule
     */
    public Schedule $schedule;
    /**
     * @var Service
     */
    public Service $service;

    public Purchase $purchase;

    /**
     * @var StripeClient
     */
    protected StripeClient $stripe;

    public function execute(
        Schedule $schedule,
        PurchaseScheduleRequest $request,
        $paymentMethodId,
        $cost,
        Purchase $purchase
    ) {
        $this->customer = $request->user();
        $this->stripe = app()->make(StripeClient::class);
        $this->schedule = $schedule;
        $this->service = $this->schedule->service;
        $this->purchase = $purchase;

        $deposit = $this->chargeDeposit($paymentMethodId);
        $subscription = $this->chargeInstallment($paymentMethodId, $cost, $request->installments);

        $this->purchase->stripe_id = $deposit->id;
        if ($subscription !== null) {
            $this->purchase->subscription_id = $subscription->id;
        }
        $purchase->deposit_amount = $this->schedule->deposit_amount;
        $purchase->save();
        $purchase->bookings()->first()->installmentComplete();

        Log::channel('stripe_installment_success')->info('Installment successfully created', [
            'user_id'        => $request->user()->id,
            'service_id'     => $schedule->service->id,
            'schedule_id'    => $this->schedule->id,
            'amount'         => $cost
        ]);
    }

    protected function chargeDeposit($paymentMethodId): \Stripe\PaymentIntent
    {
        $depositAmount = $this->schedule->deposit_amount;

        $paymentIntent = $this->stripe->paymentIntents->create(
            [
                'amount'               => $depositAmount * 100,
                'currency'             => config('app.platform_currency'),
                'payment_method_types' => ['card'],
                'customer'             => Auth::user()->stripe_customer_id,
                'payment_method'       => $paymentMethodId
            ]
        );

        $paymentIntent = $this->stripe->paymentIntents->confirm(
            $paymentIntent->id,
            [
                'payment_method' => $paymentMethodId
            ]
        );

        $installment = new Instalment();
        $installment->user_id = $this->customer->id;
        $installment->purchase_id = $this->purchase->id;
        $installment->payment_date = date('Y-m-d H:i:s');
        $installment->payment_amount = $depositAmount;
        $installment->is_paid = true;
        $installment->save();

        Log::channel('stripe_installment_success')->info('Charge deposit: ', [
            'user_id'        => Auth::user()->id,
            'service_id'     => $this->service->id,
            'schedule_id'    => $this->schedule->id,
            'amount'         => $depositAmount
        ]);

        return $paymentIntent;
    }

    protected function chargeInstallment($paymentMethodId, $cost, int $installments): ?\Stripe\Subscription
    {
        $calendarInstallments = $this->schedule->calculateInstallmentsCalendar($cost, $installments);
        $installmentInfo = $this->schedule->getInstallmentInfo($cost, $installments);
        if (count($calendarInstallments)) {
            $finalInstallmentDate = $installmentInfo['finalPaymentDate'];

            $stripePrice = $this->stripe->prices->create(
                [
                    'currency'    => config('app.platform_currency'),
                    'product'     => $this->service->stripe_id,
                    'unit_amount' => $installmentInfo['amountPerPeriod'] * 100,
                    'recurring'   => ['interval' => 'day', 'interval_count' => $installmentInfo['daysPerPeriod']],
                ]
            );
            $subscription = $this->stripe->subscriptions->create(
                [
                    'default_payment_method' => $paymentMethodId,
                    'customer'               => $this->customer->stripe_customer_id,
                    'cancel_at'              => $finalInstallmentDate->timestamp,
                    'items'                  => [
                        ['price' => $stripePrice->id],
                    ],
                ]
            );

            if ($subscription !== null) {

                foreach ($calendarInstallments as $date => $value) {
                    $installment = new Instalment();
                    $installment->user_id = $this->customer->id;
                    $installment->purchase_id = $this->purchase->id;
                    $installment->payment_date = Carbon::parse($date);
                    $installment->payment_amount = $value;
                    $installment->save();
                }

                Log::channel('stripe_installment_success')->info('Create installments: ', [
                    'user_id'        => Auth::user()->id,
                    'service_id'     => $this->service->id,
                    'schedule_id'    => $this->schedule->id,
                    'amount'         => $installmentInfo['amountPerPeriod'],
                    'daysInPeriod'         => $installmentInfo['daysPerPeriod']
                ]);

            }

            return $subscription;
        }
        return null;
    }
}
