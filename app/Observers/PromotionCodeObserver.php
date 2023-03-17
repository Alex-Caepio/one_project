<?php

namespace App\Observers;

use App\Models\Promotion;
use App\Models\PromotionCode;
use Carbon\Carbon;

class PromotionCodeObserver {

    /**
     * Handle the promotion "deleting" event.
     * Save status
     *
     * @param \App\Models\PromotionCode $promotionCode
     * @return void
     */
    public function deleting(PromotionCode $promotionCode): void {
        $promotionCode->status = PromotionCode::STATUS_DELETED;
        $promotionCode->saveQuietly();
    }

}
