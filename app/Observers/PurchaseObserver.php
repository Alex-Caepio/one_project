<?php

namespace App\Observers;

use App\Models\Purchase;

class PurchaseObserver {
    /**
     * Handle booking creation.
     *
     * @param \App\Models\Purchase $purchase
     * @return void
     */
    public function creating(Purchase $purchase) {
        if (!$purchase->reference) {
            do {
                $reference = unique_string(8);
            } while (Purchase::where('reference', $reference)->exists());
            $purchase->reference = $reference;
        }
    }
}
