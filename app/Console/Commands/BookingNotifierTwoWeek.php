<?php

namespace App\Console\Commands;

use App\Events\BookingReminder;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BookingNotifierTwoWeek extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:notifier-twoweek';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify clients about their upcoming bookings';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $bookings = Booking::query()
            ->whereNull('cancelled_at')
            ->whereRaw(
                "DATE_FORMAT(`datetime_from`, '%Y-%m-%d') = ?",
                Carbon::now()->addDays(14)->format('Y-m-d')
            )
            ->whereHas('schedule.service', static function ($query) {
                $query->where('service_type_id', 'retreat');
            })
            ->with(['user', 'user.user_timezone', 'schedule', 'schedule.service', 'practitioner'])
            ->get();
        foreach ($bookings as $booking) {
            event(new BookingReminder($booking, 'Booking Reminder - Retreat'));
        }

        Log::channel('console_commands_handler')
            ->info('Booking Reminder - Retreat two weeks. Done...',
                ['bookings_count' => count($bookings)]);
    }
}
