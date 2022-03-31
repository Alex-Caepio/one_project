<?php

namespace App\Console\Commands;

use App\Actions\RescheduleRequest\RescheduleRequestDecline;
use App\Models\RescheduleRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RescheduleNoReplyFinishCommand extends Command
{

    protected $signature = 'reschedule:noreply-finish';

    protected $description = 'Finalize reschedule requests';

    public function handle(): void
    {
        $rescheduleRequests = RescheduleRequest::query()
            ->has('old_schedule')
            ->whereRaw(
                "DATE_FORMAT(`created_at`, '%Y-%m-%d') = ?",
                Carbon::now()->subDays(2)->format('Y-m-d')
            )
            ->orWhereHas('old_schedule', static function ($query) {
                $query->whereRaw(
                    "DATE_FORMAT(`start_date`, '%Y-%m-%d') = ?",
                    Carbon::now()->subDays(6)->format('Y-m-d')
                );
            })
            ->get();
        foreach ($rescheduleRequests as $rr) {
            $rr->automatic = true;
            run_action(RescheduleRequestDecline::class, $rr);
        }
        Log::channel('console_commands_handler')
            ->info('Finalize reschedule requests',
                ['bookings_count' => count($rescheduleRequests)]);
    }
}
