<?php

namespace App\Console\Commands;

use App\Models\ScheduleFreeze;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScheduleFreezesByCron extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule-freezes:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncates freezers every 15 minutes';


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void {
        $deletedCount = ScheduleFreeze::where('freeze_at', '<', Carbon::now()->subMinutes(15))->delete();
        $this->comment(__METHOD__ . ': Freeze delete ' . $deletedCount);
    }
}
