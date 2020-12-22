<?php


namespace App\Filters;

use App\Http\Requests\Request;
use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PromotionFiltrator {
    /**
     * @param \Illuminate\Database\Eloquent\Builder $queryBuilder
     * @param \App\Http\Requests\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Builder $queryBuilder, Request $request): Builder {

        if ($request->filled('search')) {
            $search = '%' . $request->get('search') . '%';
            $queryBuilder->where(function($query) use ($search) {
                $query->where('name', 'LIKE', $search)
                      ->orWhereHas('promotion_codes', function($userQuery) use ($search) {
                          $userQuery->where('name', 'LIKE', $search);
                      })->orWhereHas('service_types', function($userQuery) use ($search) {
                        $userQuery->where('name', 'LIKE', $search);
                    })->orWhereHas('disciplines', function($userQuery) use ($search) {
                        $userQuery->where('name', 'LIKE', $search);
                    })->orWhereHas('practitioners', function($userQuery) use ($search) {
                        $userQuery->where('email', 'LIKE', $search);
                    });
            });
        }

        if ($request->filled('valid_from')) {
            $queryBuilder->whereRaw("DATE_FORMAT(`valid_from`, '%Y-%m-%d') = ?", $request->get('valid_from'));
        }

        if ($request->filled('expiry_date')) {
            $queryBuilder->whereRaw("DATE_FORMAT(`expiry_date`, '%Y-%m-%d') = ?", $request->get('expiry_date'));
        }

        $discountType = $request->getArrayFromRequest('discount_type');
        if (count($discountType)) {
            $queryBuilder->whereIn('discount_type', $discountType);

            if ($request->filled('discount_value')) {
                [$from, $to] = explode(':', $request->get('discount_value'));
                $queryBuilder->where('discount_value', '>=', (int)$from);
                if ($to !== null) {
                    $queryBuilder->where('discount_value', '<=', (int)$to);
                }
            }
        }

        $statuses = $request->getArrayFromRequest('status');
        if (count($statuses)) {
            $queryBuilder->whereIn('status', $statuses);
            if (in_array(Promotion::STATUS_DELETED, $statuses, true)) {
                $queryBuilder->withTrashed();
            }
        } else {
            $queryBuilder->withTrashed();
        }


        $appliedTo = $request->getArrayFromRequest('applied_to');
        if (count($appliedTo)) {
            $queryBuilder->whereIn('applied_to', $appliedTo);
        }

        if ($request->filled('spend_min')) {
            $queryBuilder->where('spend_min', '>=', $request->get('spend_min'));
        }

        if ($request->filled('spend_max')) {
            $queryBuilder->where('spend_max', '<=', $request->get('spend_max'));
        }

        return $queryBuilder;
    }
}
