<?php

namespace App\Console\Commands;

use App\Models\Instalment;
use App\Models\Transfer;
use App\Services\PaymentSystem\Entities\Invoice;
use App\Services\PaymentSystem\SubscriptionServiceInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

/**
 * Updates instalments of subscribers and deposits.
 */
class UpdateInstalments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instalments:update-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates payment statuses of instalments for subscriptions and deposits.';

    private SubscriptionServiceInterface $service;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SubscriptionServiceInterface $service)
    {
        $this->service = $service;

        parent::__construct();
    }

    public function handle(): void
    {
        $instalmentGroups = $this->getGroupsOfUnpaidInstalments();
        /** @var array<int,Invoice> $invoices */
        $latestInvoices = [];

        foreach ($instalmentGroups as $subscriptionId => $group) {
            $invoices = $this->service->getSubscriptionInvoices($subscriptionId)->removeEmpty();

            foreach ($invoices as $invoice) {
                // Finds by dates due to the same common value. The system creates instalements in the DB without
                // any Stripe IDs, because they don't exist yet.
                /** @var Instalment|null $instalment */
                $instalment = $group->firstWhere('payment_date', $invoice->paidAt->startOfDay());

                if ($instalment && $instalment->payment_amount === $invoice->amountPaid) {
                    $latestInvoices[$instalment->id] = $invoice;
                }
            }
        }

        $this->registerTransfersOfInstalmentsByInvoices($latestInvoices);
    }

    /**
     * @return Collection<string,Collection>
     */
    private function getGroupsOfUnpaidInstalments(): Collection
    {
        return Instalment::query()
            ->where('payment_date', '<=', Carbon::now())
            ->unpaid()
            ->whereNotNull('subscription_id')
            ->get()
            ->groupBy('subscription_id')
        ;
    }

    /**
     * @param array<int,Invoice> $invoices
     */
    private function registerTransfersOfInstalmentsByInvoices(array $invoices): void
    {
        $instalmentIds = array_keys($invoices);
        /** @var Instalment[] $instalments */
        $instalments = Instalment::whereIn('id', $instalmentIds)->get();

        foreach ($instalments as $instalment) {
            $invoice = $invoices[$instalment->id];

            $transfer = $this->createTransfer($instalment, $invoice);
            $this->updateStatusOfInstalment($instalment, $invoice, $transfer);
        }
    }

    private function createTransfer(Instalment $instalment, Invoice $invoice): Transfer
    {
        $transfer = new Transfer();
        $transfer->user_id = $instalment->purchase->service->user_id;
        $transfer->stripe_account_id = $instalment->purchase->service->practitioner->stripe_account_id;
        $transfer->stripe_transfer_id = $invoice->transfer->id;
        $transfer->is_installment = true;
        $transfer->status = 'success';
        $transfer->amount = $invoice->transfer->amount;
        $transfer->amount_original = $invoice->amountPaid;
        $transfer->currency = $invoice->transfer->currency;
        $transfer->schedule_id = $instalment->purchase->schedule_id;
        $transfer->purchase_id = $instalment->purchase->id;
        $transfer->description = 'transfer of an instalment';
        $transfer->save();

        return $transfer;
    }

    private function updateStatusOfInstalment(Instalment $instalment, Invoice $invoice, Transfer $transfer): void
    {
        $instalment->is_paid = 1;
        $instalment->stripe_invoice_id = $invoice->id;
        $instalment->paid_at = $invoice->paidAt;
        $instalment->transfer_id = $transfer->id;
        $instalment->stripe_charge_id = $invoice->chargeId;
        $instalment->stripe_payment_id = $invoice->paymentIntentId;
        $instalment->save();
    }
}
