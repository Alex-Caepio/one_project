<?php

namespace App\Console;

use App\Console\Commands\BookingNotifierTomorrow;
use App\Console\Commands\BookingNotifierTwoWeek;
use App\Console\Commands\BookingNotifierWeek;
use App\Console\Commands\InstalmentsNotifier;
use App\Console\Commands\MarkExpiredPromocodes;
use App\Console\Commands\ScheduleFreezesByCron;
use App\Console\Commands\ScheduleFreezesTruncate;
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
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        $schedule->command('schedule-freezes:cleanup')->everyFifteenMinutes();
        $schedule->command('mark-expired-promo')->everyFifteenMinutes();
        $schedule->command('bookings:notifier-week')->daily();
        $schedule->command('bookings:notifier-twoweek')->daily();
        $schedule->command('bookings:notifier-tomorrow')->daily();
        $schedule->command('reschedule:noreply')->daily();
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
