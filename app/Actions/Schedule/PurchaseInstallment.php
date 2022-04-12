<?php


namespace App\Actions\Schedule;

use App\DTO\Schedule\PaymentIntentDto;
use App\Http\Requests\Schedule\PurchaseScheduleRequest;
use App\Models\Booking;
use App\Models\Instalment;
use App\Models\PractitionerCommission;
use App\Models\Purchase;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
        Booking $booking
    ): PaymentIntentDto {
        $metadata = $this->collectMetadata($schedule, $booking);
        /** @var User $customer */
        $customer = $request->user();
        $stripe = app()->make(StripeClient::class);
        $deposit = $this->chargeDeposit($paymentMethodId, $stripe, $schedule, $customer->id, $purchase, $metadata);
        $subscription = $this->chargeInstallment(
            $paymentMethodId,
            $cost,
            $request->installments,
            $schedule,
            $purchase,
            $customer,
            $stripe,
            $metadata
        );

        $chargeId = $deposit->charges->data ? $deposit->charges->data[0]['id'] : null;
        if ($chargeId === null) {
            throw new \Exception('Installment deposit charge not found');
        }

        $purchase->stripe_id = $deposit->id;

        if ($subscription !== null) {
            $purchase->subscription_id = $subscription->id;
        }

        $purchase->deposit_amount = $schedule->deposit_amount;
        $purchase->save();

        Log::channel('stripe_installment_success')
            ->info('Installment successfully created', [
                'user_id' => $request->user()->id,
                'service_id' => $schedule->service->id,
                'schedule_id' => $schedule->id,
                'amount' => $cost
            ]);

        return new PaymentIntentDto(
            $deposit->status,
            $deposit->client_secret,
            $deposit->confirmation_method,
            $deposit->next_action,
            $chargeId,
        );
    }

    private function chargeDeposit(
        $paymentMethodId,
        StripeClient $stripe,
        Schedule $schedule,
        int $customerId,
        Purchase $purchase,
        array $metadata
    ): PaymentIntent {
        $depositAmount = $schedule->deposit_amount * $purchase->amount;
        $reference = implode(', ', $purchase->bookings->pluck('reference')->toArray());

        $paymentIntent = $stripe->paymentIntents->create(
            [
                'amount' => intval(round(($depositAmount * 100))),
                'currency' => config('app.platform_currency'),
                'payment_method_types' => ['card'],
                'customer' => Auth::user()->stripe_customer_id,
                'payment_method' => $paymentMethodId,
                'metadata' => $metadata,
            ]
        );

        $paymentIntent = $stripe->paymentIntents->confirm(
            $paymentIntent->id,
            [
                'payment_method' => $paymentMethodId
            ]
        );

        $installment = new Instalment();
        $installment->user_id = $customerId;
        $installment->purchase_id = $purchase->id;
        $installment->payment_date = date('Y-m-d H:i:s');
        $installment->payment_amount = $depositAmount;
        $installment->is_paid = true;
        $installment->reference = $reference;
        $installment->is_deposit = true;
        $installment->save();

        $chargeId = $paymentIntent->charges->data ? $paymentIntent->charges->data[0]['id'] : null;

        Log::channel('stripe_installment_success')
            ->info('Charge deposit: ', [
                'user_id' => Auth::user()->id,
                'service_id' => $schedule->id,
                'schedule_id' => $schedule->id,
                'amount' => $depositAmount,
                'charge_id' => $chargeId,
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
        StripeClient $stripe,
        array $metadata
    ): ?Subscription {
        $calendarInstallments = $schedule->calculateInstallmentsCalendar($cost, $purchase->amount, $installments);
        $installmentInfo = $schedule->getInstallmentInfo($cost, $purchase->amount, $installments);
        $reference = implode(', ', $purchase->bookings->pluck('reference')->toArray());

        if (count($calendarInstallments) === 0) {
            return null;
        }

        $stripePrice = $stripe->prices->create(
            [
                'currency' => config('app.platform_currency'),
                'product' => $schedule->service->stripe_id,
                'unit_amount' => intval(round($installmentInfo['amountPerPeriod'] * 100)),
                'recurring' => ['interval' => 'day', 'interval_count' => $installmentInfo['daysPerPeriod']],
            ]
        );

        // get practitioner commission for subscription
        $practitioner = $schedule->service->user;
        $practitionerCommissions = $this->getPractitionerRate($practitioner);
        $toTransferPercent = 100 - $practitionerCommissions;

        // create subscription
        $subscription = $stripe->subscriptions->create(
            [
                'default_payment_method' => $paymentMethodId,
                'customer' => $customer->stripe_customer_id,
                'cancel_at' => $installmentInfo['installmentCancelDate']->timestamp,
                'trial_end' => $installmentInfo['startPaymentDate']->timestamp,
                'items' => [
                    [
                        'price' => $stripePrice->id,
                        'metadata' => $metadata
                    ],
                ],
                'transfer_data' => [
                    'destination' => $practitioner->stripe_account_id,
                    'amount_percent' => $toTransferPercent,
                ],
                'metadata' => $metadata
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
            $installment->reference = $reference;
            $installment->subscription_id = $subscription->id;
            $installment->save();
        }

        Log::channel('stripe_installment_success')
            ->info('Create installments: ', [
                'user_id' => Auth::user()->id,
                'service_id' => $schedule->service->id,
                'schedule_id' => $schedule->id,
                'amount' => $installmentInfo['amountPerPeriod'],
                'reference' => $reference,
                'subscription_id' => $subscription->id,
                'daysInPeriod' => $installmentInfo['daysPerPeriod']
            ]);

        return $subscription;
    }

    private function collectMetadata(Schedule $schedule, Booking $booking): array
    {
        $practitioner = $schedule->service->practitioner;
        $client = $booking->user;

        return [
            'Practitioner business email' => $practitioner->business_email ?? "",
            'Practitioner business name' => $practitioner->business_name ?? "",
            'Practitioner stripe id' => $practitioner->stripe_customer_id ?? "",
            'Practitioner connected account id' => $practitioner->stripe_account_id ?? "",
            'Client first name' => $client->first_name ?? "",
            'Client last name' => $client->last_name ?? "",
            'Client stripe id' => $client->stripe_customer_id ?? "",
            'Booking reference' => $booking->reference ?? "",
            'Type' => 'Installment Purchase',
        ];
    }

    private function getPractitionerRate(User $practitioner)
    {
        $rate = PractitionerCommission::query()
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

        return $rate->rate ?? $practitioner->plan->commission_on_sale;
    }
}
