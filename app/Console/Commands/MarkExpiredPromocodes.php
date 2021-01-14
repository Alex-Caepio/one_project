<?php

namespace App\Console\Commands;

use App\Models\Promotion;
use App\Models\Purchase;
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
                $cntPromocodes = $promo->promotion_codes->count();
                $cntLimit =  $cntPromocodes > 0 ? $promo->promotion_codes->first()->uses_per_code * $cntPromocodes : 0;
                $cntPurchases = Purchase::whereIn('promocode_id', $promo->promotion_codes->pluck('id'))->count();
                $promo->status = $cntPurchases === $cntLimit ? Promotion::STATUS_COMPLETE : Promotion::STATUS_EXPIRED;
                $promo->save();
            }
        }
        return 0;
    }
}
