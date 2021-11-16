<?php


namespace App\Actions\Schedule;

use App\DTO\Schedule\PaymentIntentDto;
use App\Http\Requests\Schedule\PurchaseScheduleRequest;
use App\Models\Instalment;
use App\Models\Purchase;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\PaymentIntent;
use Stripe\StripeClient;
use Stripe\Subscription;

class PurchaseInstallment
{
    public function execute(
        Schedule $schedule,
        PurchaseScheduleRequest $request,
        $paymentMethodId,
        $cost,
        Purchase $purchase,
        array $stripeCreateOverrides
    ): PaymentIntentDto {
        /** @var User $customer */
        $customer = $request->user();
        $stripe = app()->make(StripeClient::class);
        $deposit = $this->chargeDeposit($paymentMethodId, $stripe, $schedule, $customer->id, $purchase->id, $stripeCreateOverrides);
        $subscription = $this->chargeInstallment($paymentMethodId, $cost, $request->installments, $schedule, $purchase, $customer, $stripe);

        $purchase->stripe_id = $deposit->id;

        if ($subscription !== null) {
            $purchase->subscription_id = $subscription->id;
        }

        $purchase->deposit_amount = $schedule->deposit_amount;
        $purchase->save();
        $purchase->bookings()->first()->installmentComplete();

        Log::channel('stripe_installment_success')->info('Installment successfully created', [
            'user_id'        => $request->user()->id,
            'service_id'     => $schedule->service->id,
            'schedule_id'    => $schedule->id,
            'amount'         => $cost
        ]);

        return new PaymentIntentDto(
            $deposit->status,
            $deposit,
            $deposit->client_secret,
            $deposit->confirmation_method,
            $deposit->next_action
        );
    }

    private function chargeDeposit(
        $paymentMethodId,
        StripeClient $stripe,
        Schedule $schedule,
        int $customerId,
        int $purchaseId,
        array $stripeCreateOverrides
    ): PaymentIntent {

        $depositAmount = $schedule->deposit_amount;

        $paymentIntent = $stripe->paymentIntents->create(
            array_merge([
                'amount'               => (int)($depositAmount * 100),
                'currency'             => config('app.platform_currency'),
                'payment_method_types' => ['card'],
                'customer'             => Auth::user()->stripe_customer_id,
                'payment_method'       => $paymentMethodId
            ], $stripeCreateOverrides)
        );

        $paymentIntent = $stripe->paymentIntents->confirm(
            $paymentIntent->id,
            [
                'payment_method' => $paymentMethodId
            ]
        );

        $installment = new Instalment();
        $installment->user_id = $customerId;
        $installment->purchase_id = $purchaseId;
        $installment->payment_date = date('Y-m-d H:i:s');
        $installment->payment_amount = $depositAmount;
        $installment->is_paid = true;
        $installment->save();

        Log::channel('stripe_installment_success')->info('Charge deposit: ', [
            'user_id'        => Auth::user()->id,
            'service_id'     => $schedule->id,
            'schedule_id'    => $schedule->id,
            'amount'         => $depositAmount
        ]);

        return $paymentIntent;
    }

    private function chargeInstallment(
        $paymentMethodId,
        $cost,
        int $installments,
        Schedule $schedule,
        Purchase $purchase,
        User $customer,
        StripeClient $stripe
    ): ?Subscription {
        $calendarInstallments = $schedule->calculateInstallmentsCalendar($cost, $installments);
        $installmentInfo = $schedule->getInstallmentInfo($cost, $installments);

        if (count($calendarInstallments) === 0) {
            return null;
        }

        $finalInstallmentDate = $installmentInfo['finalPaymentDate'];

        $stripePrice = $stripe->prices->create(
            [
                'currency'    => config('app.platform_currency'),
                'product'     => $schedule->service->stripe_id,
                'unit_amount' => (int)($installmentInfo['amountPerPeriod'] * 100),
                'recurring'   => ['interval' => 'day', 'interval_count' => $installmentInfo['daysPerPeriod']],
            ]
        );

        $subscription = $stripe->subscriptions->create(
            [
                'default_payment_method' => $paymentMethodId,
                'customer'               => $customer->stripe_customer_id,
                'cancel_at'              => $finalInstallmentDate->timestamp,
                'items'                  => [
                    ['price' => $stripePrice->id],
                ],
            ]
        );

        if ($subscription === null) {
            return null;
        }

        foreach ($calendarInstallments as $date => $value) {
            $installment = new Instalment();
            $installment->user_id = $customer->id;
            $installment->purchase_id = $purchase->id;
            $installment->payment_date = Carbon::parse($date);
            $installment->payment_amount = $value;
            $installment->save();
        }

        Log::channel('stripe_installment_success')->info('Create installments: ', [
            'user_id'        => Auth::user()->id,
            'service_id'     => $schedule->service->id,
            'schedule_id'    => $schedule->id,
            'amount'         => $installmentInfo['amountPerPeriod'],
            'daysInPeriod'   => $installmentInfo['daysPerPeriod']
        ]);

        return $subscription;
    }
}
