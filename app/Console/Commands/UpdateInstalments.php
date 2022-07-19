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
        /** @var Collection[] $instalmentGroups */
        $instalmentGroups = Instalment::query()
            ->where('payment_date', '<=', Carbon::now())
            ->unpaid()
            ->whereNotNull('subscription_id')
            ->get()
            ->groupBy('subscription_id')
        ;

        $latestPayments = [];
        $latestInvoices = [];

        foreach ($instalmentGroups as $subscriptionId => $group) {
            $invoices = $this->service->getSubscriptionInvoices($subscriptionId)->removeEmpty();

            foreach ($invoices as $invoice) {
                // Finds by dates due to the same common value.
                /** @var Instalment|null $instalment */
                $instalment = $group->firstWhere('payment_date', $invoice->paidAt->startOfDay());

                if ($instalment && $instalment->payment_amount === $invoice->amountPaid) {
                    $latestPayments[] = $instalment->id;
                    $latestInvoices[$instalment->id] = $invoice;
                }
            }
        }

        Instalment::whereIn('id', $latestPayments)->update([
            'is_paid' => 1,
        ]);

        $this->registerTransfersForInstalmentsByInvoices($latestPayments, $latestInvoices);
    }

    /**
     * @param int[] $instalmentIds
     * @param Invoice[] $invoices
     */
    private function registerTransfersForInstalmentsByInvoices(array $instalmentIds, array $invoices): void
    {
        /** @var Instalment[] $instalments */
        $instalments = Instalment::whereIn('id', $instalmentIds)->get();

        foreach ($instalments as $instalment) {
            $invoice = $invoices[$instalment->id];

            $transfer = new Transfer();
            $transfer->user_id = $instalment->purchase->service->user_id;
            $transfer->stripe_account_id = $instalment->purchase->service->practitioner->stripe_account_id;
            $transfer->stripe_transfer_id = $invoice->id;
            $transfer->status = 'success';
            $transfer->amount = $instalment->payment_amount;
            $transfer->amount_original = $instalment->purchase->price;
            $transfer->currency = $invoice->currency;
            $transfer->schedule_id = $instalment->purchase->schedule_id;
            $transfer->purchase_id = $instalment->purchase->id;
            $transfer->description = 'transfer for a instalment';
            $transfer->save();
        }
    }
}
