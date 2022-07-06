<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BookingStatusesUpdate extends Command
{
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
    public function handle(): void
    {
        /** @var Booking[] $bookings */
        $bookings = Booking::query()
            ->whereNotNull('datetime_from')
            ->whereHas('schedule.service', static function ($query) {
                $query->whereNotIn('services.service_type_id', config('app.dateless_service_types'));
            })
            ->active()
            ->get();

        $bookingIds = [];

        foreach ($bookings as $booking) {
            if ($booking->datetime_from->lessThan(Carbon::now('UTC'))) {
                $bookingIds[] = $booking->id;
            }
        }

        Booking::whereIn('id', $bookingIds)->update(['status' => Booking::COMPLETED_STATUS]);

        Log::channel('console_commands_handler')
            ->info('Mark bookings as completed. Done...',
                ['bookings_count' => $bookingIds]);
    }
}
