<?php


namespace App\Filters;

use App\Http\Requests\Request;
use App\Models\PromotionCode;
use Illuminate\Database\Eloquent\Builder;

class PromocodeFiltrator {
    /**
     * @param Builder $queryBuilder
     * @param Request $request
     * @return Builder
     */
    public function apply(Builder $queryBuilder, Request $request): Builder {

        if ($request->filled('search')) {
            $search = '%' . $request->get('search') . '%';
            $queryBuilder->where('name', 'LIKE', $search);
        }

        $promoId = $request->getArrayFromRequest('promotion_id');
        if (count($promoId)) {
            $queryBuilder->whereIn('promotion_id', $promoId);
        }

        $status = $request->getArrayFromRequest('status');
        if (count($status)) {
            $queryBuilder->whereIn('status', $status);
            if (in_array(PromotionCode::STATUS_DELETED, $status, true)) {
                $queryBuilder->withTrashed();
            }
        } else {
            $queryBuilder->withTrashed();
        }

        return $queryBuilder;
    }
}
