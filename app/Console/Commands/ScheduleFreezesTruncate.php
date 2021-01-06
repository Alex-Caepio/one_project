<?php

namespace App\Console\Commands;

use App\Models\ScheduleFreeze;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ScheduleFreezesTruncate extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule-freezes:truncate {days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncates too schedule freezes';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $artisanDays = $this->argument('days');
        $days = Carbon::now()->subDays($artisanDays);
        $count = ScheduleFreeze::where('freeze_at', '<', $days)->count();
        $deletedCount = ScheduleFreeze::where('freeze_at', '<', $days)->delete();
        $this->line('Freeze delete ' . $count . PHP_EOL);
    }
}
