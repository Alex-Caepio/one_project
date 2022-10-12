<?php

namespace App\Console;

use App\Console\Commands\BookingNotifierTomorrow;
use App\Console\Commands\BookingNotifierTwoWeek;
use App\Console\Commands\BookingNotifierWeek;
use App\Console\Commands\BookingStatusesUpdate;
use App\Console\Commands\InstalmentsNotifier;
use App\Console\Commands\MarkExpiredPromocodes;
use App\Console\Commands\RescheduleNoReplyCommand;
use App\Console\Commands\RescheduleNoReplyFinishCommand;
use App\Console\Commands\RescheduleNoReplyDeleteAfterPass;
use App\Console\Commands\ScheduleFreezesByCron;
use App\Console\Commands\ScheduleFreezesTruncate;
use App\Console\Commands\SubscriptionFreePeriod;
use App\Console\Commands\TestCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ScheduleFreezesTruncate::class,
        MarkExpiredPromocodes::class,
        ScheduleFreezesByCron::class,
        BookingNotifierTomorrow::class,
        BookingNotifierTwoWeek::class,
        BookingNotifierWeek::class,
        BookingStatusesUpdate::class,
        RescheduleNoReplyCommand::class,
        RescheduleNoReplyFinishCommand::class,
        RescheduleNoReplyDeleteAfterPass::class,
        SubscriptionFreePeriod::class,
        InstalmentsNotifier::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        $schedule->command('schedule-freezes:cleanup')->everyFifteenMinutes();
        $schedule->command('mark-expired-promo')->everyFifteenMinutes();
        $schedule->command('bookings:status-update')->everyFifteenMinutes();
        $schedule->command('instalments:update-payments')->hourly();

        $schedule->command('bookings:notifier-week')->daily();
        $schedule->command('bookings:notifier-twoweek')->daily();
        $schedule->command('bookings:notifier-tomorrow')->daily();

        $schedule->command('reschedule:noreply-finish')->daily();
        $schedule->command('reschedule:noreply-delete')->everyFifteenMinutes();

        $schedule->command('plan-freeperiod')->daily();
        $schedule->command('instalments:notify')->daily();

        $schedule->command('telescope:prune --hours=48')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands() {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
