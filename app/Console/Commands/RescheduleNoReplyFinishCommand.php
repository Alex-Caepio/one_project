<?php

namespace App\Console\Commands;

use App\Actions\RescheduleRequest\RescheduleRequestAccept;
use App\Actions\RescheduleRequest\RescheduleRequestDecline;
use App\Actions\RescheduleRequest\RescheduleRequestDelete;
use App\Models\RescheduleRequest;
use App\Models\Service;
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
            ->orWhereRaw(
                "DATE_FORMAT(`old_end_date`, '%Y-%m-%d') < ?",
                Carbon::now()->subDays()->format('Y-m-d')
            )
            ->get();
        foreach ($rescheduleRequests as $rr) {
            $rr->automatic = true;
            if (
                $rr->requested_by === RescheduleRequest::REQUESTED_BY_PRACTITIONER_IN_SCHEDULE &&
                in_array($rr->schedule->service->service_type_id, [Service::TYPE_EVENT, Service::TYPE_RETREAT, Service::TYPE_WORKSHOP])
            ) {
                run_action(RescheduleRequestAccept::class, $rr);
            } else if (Carbon::parse($rr->old_end_date)->format('Y-m-d') < Carbon::now()->subDays()->format('Y-m-d')) {
                run_action(RescheduleRequestDelete::class, $rr);
            } else {
                run_action(RescheduleRequestDecline::class, $rr);
            }
        }
        Log::channel('console_commands_handler')
            ->info('Finalize reschedule requests',
                ['bookings_count' => count($rescheduleRequests)]);
    }
}
