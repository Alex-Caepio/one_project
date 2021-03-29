<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Carbon\Carbon;
use Carbon\Exceptions\Exception;
use Illuminate\Console\Command;

class BookingStatusesUpdate extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:status-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make status Completed';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void {
        Booking::whereIn('status', ['upcoming', 'rescheduled'])->where('datetime_from', '<', Carbon::now())->whereNull('cancelled_at')->update(['status' => 'completed']);
    }
}
