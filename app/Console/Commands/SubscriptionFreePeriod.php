<?php

namespace App\Console\Commands;

use App\Events\BookingReminder;
use App\Events\ChangeOfSubscription;
use App\Models\Booking;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
        $nowDate = Carbon::now()->endOfDay();
        $freePlan = Plan::where('is_free', true)->orderBy('id', 'ASC')->first();

        $practitioners = User::where('account_type', User::ACCOUNT_PRACTITIONER)
                             ->whereHas('plan', static function($query) use ($nowDate) {
                                 $query->whereNotNull('plans.free_start_to')
                                       ->where('plans.free_start_to', '<=', $nowDate->format('Y-m-d H:i:s'));
                             })->whereNull('stripe_plan_id')->with('plan')->get();

        Log::channel('console_commands_handler')
           ->info('All practitioners with expired plans will be switched to new plan: ', [
               'plan_id'          => $freePlan->id ?? null,
               'date'             => $nowDate->format('d.m.Y'),
               'practitionersCnt' => $practitioners->count(),
           ]);

        $practitioners->each(static function($user, $key) use ($freePlan) {
            if ($freePlan instanceof Plan) {
                $user->plan_id = $freePlan->id;
                $user->plan_until = null;
                $user->plan_from = Carbon::now();
                $user->save();
                event(new ChangeOfSubscription($user, $freePlan, null));
            } else {
                $user->plan_id = null;
                $user->plan_until = null;
                $user->plan_from = null;
                $user->save();
            }
        });
    }
}
