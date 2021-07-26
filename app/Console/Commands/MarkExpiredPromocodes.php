<?php

namespace App\Console\Commands;

use App\Models\Promotion;
use App\Models\PromotionCode;
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
        Log::channel('promotion_status_update')->info('Promotion status check...');
        $promotions = Promotion::where('status', Promotion::STATUS_ACTIVE)->with('promotion_codes')->get();

        foreach ($promotions as $promo) {
            $cntPromocodes = $promo->promotion_codes->count();
            $completePromotionCodes =
                $promo->promotion_codes()->where('status', PromotionCode::STATUS_COMPLETE)->count();
            $cntPurchases =
                Purchase::whereIn('promocode_id', $promo->promotion_codes()->pluck('promotion_codes.id')->toArray())
                        ->count();
            $promoCode = $promo->promotion_codes->first();
            $usesPerCode = $promoCode->uses_per_code ?: 1;
            $cntLimit = $usesPerCode * $cntPromocodes;


            if ($cntPromocodes > 0 && $cntPromocodes === $completePromotionCodes) {
                $promo->status = Promotion::STATUS_COMPLETE;
                $promo->save();
                Log::channel('promotion_status_update')->info('Mark promotion complete', [
                    'promotion_id' => $promo->id,
                    'complete'     => $completePromotionCodes,
                    'status'       => $promo->status,
                    'reason'       => 'By status complete in promocodes',
                ]);
            } elseif ($cntPurchases > 0 && $cntPurchases === $cntLimit) {
                $promo->status = Promotion::STATUS_COMPLETE;
                $promo->save();
                Log::channel('promotion_status_update')->info('Mark promotion complete', [
                    'promotion_id' => $promo->id,
                    'cntPurchase'  => $cntPurchases,
                    'cntLimit'     => $cntLimit,
                    'status'       => $promo->status,
                    'reason'       => 'By empty expiry date',
                ]);
            } elseif ($promo->expiry_date && Carbon::parse($promo->expiry_date) < Carbon::now()) {
                $promo->status = Promotion::STATUS_EXPIRED;
                $promo->save();
                Log::channel('promotion_status_update')->info('Mark promotion expired:', [
                    'promotion_id' => $promo->id,
                    'cntPurchase'  => $cntPurchases,
                    'cntLimit'     => $cntLimit,
                    'status'       => $promo->status,
                    'reason'       => 'By Expiry Date',
                ]);
            }

            return 0;
        }
    }
}
