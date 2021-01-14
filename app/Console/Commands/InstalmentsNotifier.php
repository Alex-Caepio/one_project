<?php

namespace App\Console\Commands;

use App\Events\InstalmentPaymentReminder;
use App\Models\Instalment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class InstalmentsNotifier extends Command {
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
    public function handle(): void {
        $paymentDate = Carbon::now()->addDays(7);
        $installments =
            Instalment::whereRaw("DATE_FORMAT(`payment_date`, '%Y-%m-%d') = ?", $paymentDate->format('Y-m-d'))
                      ->with(['user','purchase','purchase.schedule','purchase.service','purchase.service.user'])
                      ->get();
        Log::info('Found future installments' . count($installments));
        foreach ($installments as $installment) {
            $userPaymentSchedules = Instalment::where('user_id', $installment->user_id)
                                              ->where('payment_date',
                                                      '>',
                                                      $paymentDate->startOfDay()->format('Y-m-d H:i:s'))
                                              ->get();
            event(new InstalmentPaymentReminder($installment, $userPaymentSchedules));
        }
    }
}
