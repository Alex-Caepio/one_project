<?php

namespace App\Console\Commands;

use App\Events\InstalmentPaymentReminder;
use App\Models\Instalment;
use App\Models\Purchase;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class InstalmentsNotifier extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instalments:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Payment notification for installments';


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $paymentDate = Carbon::now()->addDays(7);
        $purchases = Purchase::query()
            ->whereHas(
                'instalments',
                static function ($instQuery) use ($paymentDate) {
                    $instQuery
                        ->whereRaw("DATE_FORMAT(`payment_date`, '%Y-%m-%d') = ?", $paymentDate->format('Y-m-d'))
                        ->where('is_paid', 0);
                }
            )
            ->where('is_deposit', 1)
            ->whereNull('cancelled_at_subscription')
            ->get();

        Log::channel('console_commands_handler')->info(
            'Purchases with installments payment date: ',
            [
                'payment_date' => $paymentDate->format('Y-m-d'),
                'count_purchases' => count($purchases)
            ]
        );
        foreach ($purchases as $purchase) {
            $userPaymentSchedule = Instalment::where('purchase_id', $purchase->id)
                ->whereRaw(
                    "DATE_FORMAT(`payment_date`, '%Y-%m-%d') = ?",
                    $paymentDate->startOfDay()->format('Y-m-d')
                )
                ->where('is_paid', 0)
                ->orderBy('payment_date')
                ->first();
            if ($userPaymentSchedule) {
                Log::channel('console_commands_handler')
                    ->info(
                        'Next installment for purchase: ',
                        [
                            'purchase_id' => $purchase->id,
                            'installmentID' => $userPaymentSchedule->id,
                        ]
                    );
                event(new InstalmentPaymentReminder($purchase, $userPaymentSchedule));
            }
        }
    }
}
