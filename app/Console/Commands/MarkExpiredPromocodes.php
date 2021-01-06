<?php

namespace App\Console\Commands;

use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MarkExpiredPromocodes extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mark-expired-promo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark Promotions as Complete Or Expired';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int {
        $promotions = Promotion::where('status', Promotion::STATUS_ACTIVE)->with('promotion_codes')->get();
        foreach ($promotions as $promo) {
            Log::info(__METHOD__.': Processing promotion: ' . $promo->name);
            if (Carbon::parse($promo->expiry_date) > Carbon::now()) {
                // @todo add additional check to usage count
                $promo->status = Promotion::STATUS_EXPIRED;
                $promo->save();
            }
        }
        return 0;
    }
}
