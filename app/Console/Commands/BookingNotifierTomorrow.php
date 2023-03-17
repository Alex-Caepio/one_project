<?php

namespace App\Console\Commands;

use App\Events\BookingReminder;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BookingNotifierTomorrow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:notifier-tomorrow';

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
        $bookingDate = Carbon::now()->addDay()->format('Y-m-d');
        $bookings = Booking::query()
            ->whereNull('cancelled_at')
            ->whereRaw("DATE_FORMAT(`datetime_from`, '%Y-%m-%d') = ?", $bookingDate)
            ->whereHas('schedule.service', static function ($query) {
                $query->whereNotIn('service_type_id', config('app.dateless_service_types'));
            })
            ->with(['user', 'user.user_timezone', 'schedule', 'schedule.service', 'practitioner'])
            ->get();
        foreach ($bookings as $booking) {
            event(new BookingReminder($booking, 'Booking Reminder - WS/Event/Retreat/Appointment'));
        }
        Log::channel('console_commands_handler')
            ->info(
                'Booking Reminder - Tomorrow event. Done...',
                ['bookings_count' => count($bookings), 'booking_date' => $bookingDate]
            );
    }
}
