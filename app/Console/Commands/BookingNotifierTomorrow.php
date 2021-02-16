<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class BookingNotifierTomorrow extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:notifier';

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
        $bookings = Booking::whereNull('reminded_at')->with(['user', 'schedule', 'schedule.service'])->get();
    }
}
