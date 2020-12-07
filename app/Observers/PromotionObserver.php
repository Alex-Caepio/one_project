<?php

namespace App\Observers;

use App\Models\Promotion;
use App\Models\PromotionCode;
use Carbon\Carbon;

class PromotionObserver {

    /**
     * Handle the promotion "deleting" event.
     * Save status
     *
     * @param \App\Models\Promotion $promotion
     * @return void
     */
    public function deleting(Promotion $promotion): void {
        $promotion->status = Promotion::STATUS_DELETED;
        $promotion->saveQuietly();
    }

    /**
     * Handle the promotion "deleted" event.
     * Mark as "Deleted" all of the codes
     *
     * @param \App\Models\Promotion $promotion
     * @return void
     */
    public function deleted(Promotion $promotion): void {
        PromotionCode::where('promotion_id', $promotion->id)->delete();
    }

}
