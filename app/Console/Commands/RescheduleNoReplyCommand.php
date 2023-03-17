<?php

namespace App\Console\Commands;

use App\Events\RescheduleRequestNoReplyFromClient;
use App\Models\RescheduleRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RescheduleNoReplyCommand extends Command
{

    protected $signature = 'reschedule:noreply';

    protected $description = 'Send practitioner notification';

    public function handle(): void
    {
        $rescheduleRequests = RescheduleRequest::query()
            ->has('old_schedule')
            ->whereNotIn('requested_by', [RescheduleRequest::REQUESTED_BY_PRACTITIONER_IN_SCHEDULE])
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
            event(new RescheduleRequestNoReplyFromClient($rr));
        }

        Log::channel('console_commands_handler')
            ->info(
                'Send practitioner notification',
                ['bookings_count' => count($rescheduleRequests)]
            );
    }
}
