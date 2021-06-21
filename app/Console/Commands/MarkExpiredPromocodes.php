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
            $cntPromocodes = $promo->promotion_codes->count();
            $cntLimit = $cntPromocodes > 0 ? $promo->promotion_codes->first()->uses_per_code * $cntPromocodes : 0;
            $cntPurchases =
                Purchase::whereIn('promocode_id', $promo->promotion_codes->pluck('promotion_codes.id'))->count();

            if ($promo->expiry_date && Carbon::parse($promo->expiry_date) < Carbon::now()) {
                $promo->status = $cntPurchases === $cntLimit &&
                                 $cntPurchases > 0 ? Promotion::STATUS_COMPLETE : Promotion::STATUS_EXPIRED;
                $promo->save();
                Log::channel('promotion_status_update')->info('Mark promotion:', [
                    'promotion_id' => $promo->id,
                    'cntPurchase'  => $cntPurchases,
                    'cntLimit'     => $cntLimit,
                    'status'       => $promo->status,
                    'reason'       => 'By Expiry Date',
                ]);
            } elseif (!$promo->expiry_date && $cntPurchases === $cntLimit && $cntPurchases > 0) {
                $promo->status = Promotion::STATUS_COMPLETE;
                $promo->save();
                Log::channel('promotion_status_update')->info('Mark promotion:', [
                    'promotion_id' => $promo->id,
                    'cntPurchase'  => $cntPurchases,
                    'cntLimit'     => $cntLimit,
                    'status'       => $promo->status,
                    'reason'       => 'By empty expiry date',
                ]);
            }
        }
        return 0;
    }
}
