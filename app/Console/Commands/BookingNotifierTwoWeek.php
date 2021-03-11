<?php

namespace App\Console\Commands;

use App\Events\BookingReminder;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BookingNotifierTwoWeek extends Command {
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
    public function handle(): void {
        $bookings = Booking::whereNull('cancelled_at')->whereRaw("DATE_FORMAT(`datetime_from`, '%Y-%m-%d') = ?",
                                                                 Carbon::now()->addDays(14)->format('Y-m-d'))
                           ->whereHas('schedule.service', static function($query) {
                               $query->where('service_type_id', 'retreat');
                           })->with(['user', 'schedule', 'schedule.service', 'practitioner'])->get();
        foreach ($bookings as $booking) {
            event(new BookingReminder($booking, 'Booking Reminder - Retreat'));
        }
    }
}