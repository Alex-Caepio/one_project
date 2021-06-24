<?php

namespace App\Console\Commands;

use App\Events\BookingReminder;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SubscriptionFreePeriod extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plan-freeperiod';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check practitioners with overdue free period';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void {
        $nowDate = Carbon::now()->startOfDay();
        //$practitioners = User::where('account_type', User::ACCOUNT_PRACTITIONER)->whereHas('plan', static function($nowDate) {

        //});
    }
}
