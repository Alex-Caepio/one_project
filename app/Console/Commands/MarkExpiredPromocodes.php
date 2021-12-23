<?php

namespace App\Console\Commands;

use App\Models\Promotion;
use App\Models\PromotionCode;
use App\Models\Purchase;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MarkExpiredPromocodes extends Command
{
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
     * @return void
     */
    public function handle(): void
    {
        Log::channel('promotion_status_update')->info('Promotion status check...');
        $promotions = Promotion::where('status', Promotion::STATUS_ACTIVE)->with('promotion_codes')->get();

        foreach ($promotions as $promo) {
            $this->updatePromotionCodes($promo->id);

            $cntPromocodes = $promo->promotion_codes()->count(); //all codes(disabled, complete, active)

            $completePromotionCodes =
                $promo->promotion_codes()->where('status', '<>', PromotionCode::STATUS_ACTIVE)->count();

            $cntPurchasesByPromocode =
                Purchase::whereIn(
                    'promocode_id',
                    $promo->promotion_codes()->pluck('promotion_codes.id')->toArray()
                )->groupBy('promocode_id')
                    ->count();

            //$promoCode = $promo->promotion_codes->first();
            //$usesPerCode = $promoCode->uses_per_code ?: 1;
            //$cntLimit = $usesPerCode * $cntPromocodes;

            Log::channel('promotion_status_update')->info(
                'Check promotion: ',
                [
                    'promotion_name' => $promo->name,
                    'totalCodes' => $cntPromocodes,
                    'totalCompletedCodes' => $completePromotionCodes,
                    'cntPurchasedPromocodes' => $cntPurchasesByPromocode
                ]
            );

            if ($cntPromocodes > 0 && $cntPromocodes === $completePromotionCodes) {
                $promo->status = Promotion::STATUS_COMPLETE;
                $promo->save();
                Log::channel('promotion_status_update')
                    ->info('Mark promotion complete', [
                        'promotion_id' => $promo->id,
                        'complete' => $completePromotionCodes,
                        'status' => $promo->status,
                        'reason' => 'By status complete in promocodes',
                    ]);
            } elseif ($promo->expiry_date && Carbon::parse($promo->expiry_date) < Carbon::now()) {
                $promo->status = Promotion::STATUS_EXPIRED;
                $promo->save();
                Log::channel('promotion_status_update')
                    ->info('Mark promotion expired:', [
                        'promotion_id' => $promo->id,
                        'status' => $promo->status,
                        'reason' => 'By Expiry Date',
                    ]);
            }
        }
    }


    private function updatePromotionCodes(int $promoId): void
    {
        $promotionCodes = PromotionCode::where('promotion_id', $promoId)->where(
            'status',
            PromotionCode::STATUS_ACTIVE
        )->get();
        $promotionCodes->map(static function ($promocode) {
            $cntPurchases = Purchase::where('promocode_id', $promocode->id)->count();
            if ($cntPurchases > 0 && $cntPurchases >= (int)$promocode->uses_per_code) {
                Log::channel('promotion_status_update')->info('Update promotion status to Complete: ',
                    ['promocode' => $promocode->name, 'purchases_cnt' => $cntPurchases]
                );
                $promocode->status = PromotionCode::STATUS_COMPLETE;
                $promocode->save();
            }

            return $promocode;
        });
    }


}
