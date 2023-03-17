<?php

namespace App\Console\Commands;

use App\Actions\RescheduleRequest\RescheduleRequestDelete;
use App\Models\RescheduleRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RescheduleNoReplyDeleteAfterPass extends Command
{
    protected $signature = 'reschedule:noreply-delete';

    protected $description = 'Delete reschedule request after the booking has passed';

    public function handle(): void
    {
        $rescheduleRequests = RescheduleRequest::query()
            ->whereRaw(
                "DATE_FORMAT(`old_end_date`, '%Y-%m-%d %H:%i') < ?",
                Carbon::now()->format('Y-m-d H:i')
            )
            ->get();

        foreach ($rescheduleRequests as $rr) {
            $rr->automatic = true;
            run_action(RescheduleRequestDelete::class, $rr);
        }

        Log::channel('console_commands_handler')
            ->info('Finalize reschedule requests',
                ['bookings_count' => count($rescheduleRequests)]);
    }
}
