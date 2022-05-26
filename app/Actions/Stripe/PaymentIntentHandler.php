<?php

namespace App\Actions\Stripe;

use App\Http\Requests\StripeRequest;
use App\Models\User;
use App\Models\Purchase;
use App\Models\Transfer;
use App\Services\MetadataService;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class PaymentIntentHandler
{

    private string $_requestInvoiceId;
    private string $_requestPaymentIntentId;
    private string $_requestPractitionerId;
    private string $_requestAmountPaid;
    private string $_requestTransferId;
    private string $_requestTransferAmount;
    private string $_requestSubscriptionId;
    private string $_requestCurrency;
    private array  $_requestMetadata;
    private array  $metadata;
    private StripeClient $stripeClient;

    private ?User $practitioner;

    public function __constructor(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    public function execute(StripeRequest $request): void
    {
        Log::info('[[' . __METHOD__ . ']]: start handle payment intent  Event: ' . $request->getEventName());

        $dataObject = $request->getObject();

        $this->_requestPaymentIntentId = $dataObject['id'];
        $this->_requestPractitionerId = $dataObject['transfer_data']['destination'];
        $this->_requestAmountPaid = round($dataObject['amount'] / 100, 2);
        $this->_requestTransferId = (array_shift($dataObject['charges']['data']))['transfer'];
        $this->_requestTransferAmount = round($dataObject['transfer_data']['amount'] / 100, 2);
        $this->_requestInvoiceId = $dataObject['invoice'];
        $this->_requestStatus = $dataObject['status'];
        $this->_requestCurrency = $dataObject['currency'];
        $this->_requestMetadata = $dataObject['metadata'];

        // Get subscription id for getting user purchase
        $this->stripeClient = app()->make(StripeClient::class);
        $invoice = $this->stripeClient->invoices->retrieve($this->_requestInvoiceId);
        $this->_requestSubscriptionId = $invoice->subscription;
        $this->retrievePractitioner();
        $this->retrievePurchase();

        // Filter payment intents. We need only subscription updates. They dont has transfer amount
        if (
            empty($this->_requestPractitionerId) && empty($this->_requestTransferAmount) && !empty($this->_requestInvoiceId)
        ) {
            // Update current payment intent metadata
            if (empty($this->_requestMetadata) && !empty($this->practitioner)) {
                $this->metadata = MetadataService::retrieveMetadataSubscription($this->practitioner);
                try {
                    $this->stripeClient->paymentIntents->update(
                        $this->_requestPaymentIntentId,
                        ['metadata' => $this->metadata]
                    );
                } catch (\Exception $e) {
                    Log::channel('stripe_webhooks_error')->info('Payment intent update error: ', [
                        'payment_intent_id' => $this->_requestPaymentIntentId,
                        'invoice_id' => $this->_requestInvoiceId,
                        'message' => $e->getMessage(),
                    ]);
                }
            }
        } else if (
            !empty($this->_requestPractitionerId)
            && !empty($this->_requestTransferAmount)
            && !empty($this->_requestTransferId)
        ) {
            try {
                // save transfer
                Transfer::create([
                    'user_id' => $this->practitioner->id,
                    'stripe_account_id' => $this->practitioner->stripe_account_id,
                    'stripe_transfer_id' => $this->_requestTransferId,
                    'status' => $this->_requestStatus === "succeeded" ? 'success' : 'fail',
                    'amount' => $this->_requestTransferAmount,
                    'amount_original' => $this->_requestAmountPaid,
                    'currency' => $this->_requestCurrency,
                    'schedule_id' => $this->purchase->schedule->id,
                    'description' => 'installment',
                    'purchase_id' => $this->purchase->id,
                    'is_installment' => true,
                ]);

                Log::channel('stripe_webhooks_info')->info(
                    "Transfer registered",
                    [
                        'stripe_client_id' => $this->_requestPractitionerId,
                        'stripe_subscription_id' => $this->_requestSubscriptionId,
                        'user_id' => $this->practitioner->id,
                    ],
                );
            } catch (\Exception $e) {
                Log::channel('stripe_webhooks_error')->info(
                    "Unpaid installment for subscription not found",
                    ['subscription_id' => $this->_requestSubscriptionId, 'error' => $e],
                );
            }

            // Add metadata to transfers to practitioners
            if (empty($this->_requestMetadata) && !empty($this->purchase)) {
                $this->metadata = MetadataService::retrieveMetadataPurchase($this->purchase, MetadataService::TYPE_INSTALLMENT);
                try {
                    $this->stripeClient->paymentIntents->update(
                        $this->_requestPaymentIntentId,
                        ['metadata' => $this->metadata]
                    );

                    $transfer = $this->stripeClient->transfers->update(
                        $this->_requestTransferId,
                        ['metadata' => $this->metadata]
                    );

                    $this->stripeClient->charges->update(
                        $transfer->destination_payment,
                        ['metadata' => $this->metadata],
                        ['stripe_account' => $transfer->destination]
                    );
                } catch (\Exception $e) {
                    Log::channel('stripe_webhooks_error')->info('Transfer metadata update error: ', [
                        'payment_intent_id' => $this->_requestPaymentIntentId,
                        'invoice_id' => $this->_requestInvoiceId,
                        'message' => $e->getMessage(),
                    ]);
                }
            }
        } else {
            Log::channel('stripe_webhooks_error')->info('Not enough data to process payment intent: ', [
                'invoice_id' => $this->_requestInvoiceId,
                'customer_id' => $this->_requestPractitionerId,
            ]);
        }
    }

    private function retrievePractitioner(): void
    {
        $this->practitioner = User::where('stripe_account_id', $this->_requestPractitionerId)
            ->where('account_type', User::ACCOUNT_PRACTITIONER)
            ->first();
    }

    private function retrievePurchase(): void
    {
        $this->purchase = Purchase::where('subscription_id', $this->_requestSubscriptionId)->first();
    }
}
