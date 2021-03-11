<?php

namespace App\Console\Commands;

use App\Events\BookingReminder;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BookingNotifierWeek extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:notifier-week';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify clients about their upcoming bookings';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): void {
        $bookings = Booking::whereNull('cancelled_at')->whereRaw("DATE_FORMAT(`datetime_from`, '%Y-%m-%d') = ?",
                                                                 Carbon::now()->addDays(7)->format('Y-m-d'))
                           ->whereHas('schedule.service', static function($query) {
                               $query->whereIn('service_type_id', ['workshop', 'event']);
                           })->with(['user', 'schedule', 'schedule.service', 'practitioner'])->get();
        foreach ($bookings as $booking) {
            event(new BookingReminder($booking, 'Booking Reminder - WS/Event'));
        }

    }
}
