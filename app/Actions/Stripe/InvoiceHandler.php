<?php

namespace App\Actions\Stripe;

use App\Http\Requests\StripeRequest;
use App\Models\Instalment;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Builder;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use App\Models\User;
use Illuminate\Support\Facades\Log;


class InvoiceHandler
{

    private string $_requestInvoiceId;
    private string $_requestSubscriptionId;
    private string $_requestCustomerId;
    private string $_requestAmountPaid;
    private array $_requestMetadata;

    private ?User $customer;
    private ?User $practitioner;
    private ?Purchase $purchase;
    private array $metadata;

    public function execute(StripeRequest $request): void
    {
        Log::info('[[' . __METHOD__ . ']]: start handle Invoice Event: ' . $request->getEventName());

        $dataObject = $request->getObject();

        if (isset($dataObject['id'], $dataObject['customer'], $dataObject['subscription'])) {
            $this->_requestInvoiceId = $dataObject['id'];
            $this->_requestCustomerId = $dataObject['customer'];
            $this->_requestSubscriptionId = $dataObject['subscription'];
            $this->_requestAmountPaid = $dataObject['amount_paid'] / 100;
            $this->_requestMetadata = $dataObject['metadata'];

            $this->retrieveCustomer();
            $this->retrievePurchase();
            $this->retrievePractitioner();

            if (!empty($this->purchase)) {
                $this->retrieveMetadataPurchase($this->purchase);
            }

            if (!empty($this->practitioner)) {
                $this->retrieveMetadataSubscription($this->practitioner);
            }

            if ($this->customer instanceof User) {
                switch ($request->getEventName()) {
                    case 'payment_succeeded':
                        $this->handlePaymentSucceeded();
                        break;

                    case 'payment_failed':
                    default:
                        break;
                }
            } else {
                Log::channel('stripe_webhooks_error')->info('User was not found: ', [
                    'invoice_id' => $this->_requestInvoiceId,
                    'customer_id' => $this->_requestCustomerId,
                    'subscription_id' => $this->_requestSubscriptionId
                ]);
            }

            if (empty($this->_requestMetadata) && !empty($this->metadata)) {
                $stripe = app()->make(StripeClient::class);
                try {
                    $stripe->invoices->update(
                        $this->_requestInvoiceId,
                        ['metadata' => $this->metadata]
                    );
                } catch (ApiErrorException $e) {
                    Log::channel('stripe_webhooks_error')->info('Invoice update error: ', [
                        'invoice_id' => $this->_requestInvoiceId,
                        'customer_id' => $this->_requestCustomerId,
                        'subscription_id' => $this->_requestSubscriptionId,
                        'message' => $e->getMessage(),
                    ]);
                }

            }
        }
    }

    private function retrieveCustomer(): void
    {
        $this->customer = User::where('stripe_customer_id', $this->_requestCustomerId)
            ->where('account_type', User::ACCOUNT_CLIENT)->with('plan')->first();
    }

    private function retrievePractitioner(): void
    {
        $this->practitioner = User::where('stripe_customer_id', $this->_requestCustomerId)
            ->where('account_type', User::ACCOUNT_PRACTITIONER)->first();
    }

    private function retrievePurchase(): void
    {
        $this->purchase = Purchase::whereHas('instalments', function (Builder $query) {
            $query->where('subscription_id', $this->_requestSubscriptionId);
        })->first();
    }

    private function handlePaymentSucceeded(): void
    {
        $instalment = Instalment::where('user_id', $this->customer->id)
            ->where('subscription_id', $this->_requestSubscriptionId)
            ->where('payment_amount', $this->_requestAmountPaid)
            ->where('is_paid', 0)
            ->orderBy('payment_date')
            ->first();

        if ($instalment) {
            $instalment->is_paid = 1;
            $instalment->save();

            Log::channel('stripe_webhooks_info')->info(
                "Instalment is paid",
                [
                    'instalment_id' => $instalment->id,
                    'stripe_client_id' => $this->_requestCustomerId,
                    'stripe_subscription_id' => $this->_requestSubscriptionId,
                    'user_id' => $this->customer->id,
                ],
            );

            return;
        }

        Log::channel('stripe_webhooks_error')->info(
            "Unpaid instalment for subscription not found",
            ['subscription_id' => $this->_requestSubscriptionId],
        );
    }

    private function retrieveMetadataPurchase(Purchase $purchase): void
    {
        $references = [];
        foreach ($purchase->bookings()->get()->unique('reference') as $booking) {
            $references[] = $booking->reference;
        }
        $referenceStr = implode(',', $references);

        $this->metadata = [
            'Practitioner business email' => $purchase->service->practitioner->business_email,
            'Practitioner business name' => $purchase->service->practitioner->business_name,
            'Practitioner stripe id' => $purchase->service->practitioner->stripe_customer_id,
            'Practitioner connected account id' => $purchase->service->practitioner->stripe_account_id,
            'Tom Commission' => $purchase->service->practitioner->getCommission() . '%',
            'Application Fee' =>
                round($purchase->price * $purchase->service->practitioner->getCommission() / 100, 2, PHP_ROUND_HALF_DOWN)
                . '(' . config('app.platform_currency') . ')',
            'Client first name' => $purchase->user->first_name,
            'Client last name' => $purchase->user->last_name,
            'Client stripe id' => $purchase->user->stripe_customer_id,
            'Booking reference' => $referenceStr,
            'Promoted by' => $purchase->promocode->promotion->applied_to ?? "",
        ];
    }

    private function retrieveMetadataSubscription(User $user): void
    {
        $this->metadata = [
            'Action' => 'Subscription update',
            'Practitioner business email' => $user->business_email,
            'Practitioner business name' => $user->business_name,
            'Practitioner stripe id' => $user->stripe_customer_id,
            'Practitioner connected account id' => $user->stripe_account_id,
        ];
    }
}
