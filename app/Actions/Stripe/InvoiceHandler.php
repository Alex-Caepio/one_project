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
use App\Services\MetadataService;

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
                $this->metadata = MetadataService::retrieveMetadataPurchase($this->purchase);
            }

            if (!empty($this->practitioner)) {
                $this->metadata = MetadataService::retrieveMetadataSubscription($this->practitioner);
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
                Log::channel('stripe_webhooks_error')->warning('User was not found: ', [
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
                    Log::channel('stripe_webhooks_error')->error('Invoice update error: ', [
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

        Log::channel('stripe_webhooks_error')->warning(
            "Unpaid instalment for subscription not found",
            ['subscription_id' => $this->_requestSubscriptionId],
        );
    }
}
