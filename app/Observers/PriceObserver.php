<?php

namespace App\Observers;

use App\Events\ArticlePublished;
use App\Events\ArticleUnpublished;
use App\Models\Article;
use App\Models\Price;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PriceObserver {

    /**
     * Handle the article "updated" event.
     *
     * @param \App\Models\Price $price
     * @return void
     */
    public function saving(Price $price): void {

        if (!$price->min_purchase) {
            $price->min_purchase = 1;
        }
    }


}
