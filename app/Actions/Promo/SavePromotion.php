<?php


namespace app\Actions\Promo;


use app\Http\Requests\Promotion\SavePromotionRequest;
use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SavePromotion {
    /**
     * @param \app\Http\Requests\Promotion\SavePromotionRequest $request
     * @param \App\Models\Promotion|null $promotion
     * @return \App\Models\Promotion
     */
    public function execute(SavePromotionRequest $request, Promotion $promotion = null): Promotion {
        if ($promotion === null) {
            $promotion = new Promotion();
        }
        DB::transaction(function() use ($promotion, $request) {
            $this->fillModel($request, $promotion);
            $promotion->save();
            $this->fillRelations($request, $promotion);
            $promocodes = run_action(CreatePromocode::class, $request, $promotion);
        });
        return $promotion;
    }


    /**
     * @param \app\Http\Requests\Promotion\SavePromotionRequest $request
     * @param \App\Models\Promotion $promotion
     */
    private function fillRelations(SavePromotionRequest $request, Promotion $promotion): void {
        if ($request->filled('focus_areas')) {
            $promotion->focus_areas()->sync($request->get('focus_areas'));
        }

        if ($request->filled('disciplines')) {
            $promotion->disciplines()->sync($request->get('disciplines'));
        }

        if ($request->filled('practitioners')) {
            $promotion->practitioners()->sync($request->get('practitioners'));
        }
    }


    /**
     * @param \app\Http\Requests\Promotion\SavePromotionRequest $request
     * @param \App\Models\Promotion $promotion
     */
    private function fillModel(SavePromotionRequest $request, Promotion $promotion): void {
        $promotion->forceFill([
                                  'name'            => $request->get('name'),
                                  'valid_date'      => $request->filled('valid_date') ? Carbon::parse($request->get('valid_date'))
                                                                                              ->startOfDay() : null,
                                  'expiry_date'     => $request->filled('expiry_date') ? Carbon::parse($request->get('expiry_date'))
                                                                                               ->endOfDay() : null,
                                  'spend_min'       => $request->get('spend_min'),
                                  'spend_max'       => $request->get('spend_max'),
                                  'service_type_id' => $request->get('service_type_id'),
                                  'status'          => Promotion::STATUS_ACTIVE,
                                  'discount_type'   => $request->get('discount_type'),
                                  'discount_value'  => $request->get('discount_value'),
                                  'applied_to'      => $request->get('applied_to')
                              ]);
    }

}
