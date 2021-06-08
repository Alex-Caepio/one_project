<?php

namespace App\Console\Commands;

use App\Events\RescheduleRequestNoReplyFromClient;
use App\Models\RescheduleRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RescheduleNoReplyFinishCommand extends Command {

    protected $signature = 'reschedule:noreply-finish';

    protected $description = 'Finalize reschedule requests';

    public function handle(): void {
        $rescheduleRequests = RescheduleRequest::has('old_schedule')
                                               ->whereRaw("DATE_FORMAT(`created_at`, '%Y-%m-%d') = ?",
                                                          Carbon::now()->subDays(10)->format('Y-m-d'))
                                               ->orWhereHas('old_schedule', static function($query) {
                                                   $query->whereRaw("DATE_FORMAT(`start_date`, '%Y-%m-%d') = ?",
                                                                    Carbon::now()->subDays(6)->format('Y-m-d'));
                                               })->get();
        foreach ($rescheduleRequests as $rr) {
            $rr->delete();
        }
    }
}
