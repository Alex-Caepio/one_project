<?php


namespace app\Actions\Promo;


use App\Http\Requests\Request;
use App\Models\Promotion;
use App\Models\PromotionCode;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CreatePromocode {


    /**
     * @param \App\Http\Requests\Request $request
     * @param \App\Models\Promotion $promotion
     * @return \Illuminate\Support\Collection
     */
    public function execute(Request $request, Promotion $promotion): Collection {
        $result = new Collection();
        if (is_array($request->get('promocode_names')) && count($request->get('promocode_names'))) {
            foreach (array_unique($request->get('promocode_names')) as $name) {
                $result->push($this->createPromotionCode(Str::upper($name), $request, $promotion));
            }
        } else {
            for ($i = 0; $i < (int)$request->get('total_codes'); $i++) {
                do {
                    $code = unique_string();
                } while (PromotionCode::where('name', $code)->exists());
                $result->push($this->createPromotionCode($code, $request, $promotion));
            }
        }
        return $result;
    }


    /**
     * @param string $code
     * @param \App\Http\Requests\Request $request
     * @param \App\Models\Promotion $promotion
     * @return \App\Models\PromotionCode
     */
    private function createPromotionCode(string $code, Request $request, Promotion $promotion): PromotionCode {
        return PromotionCode::create([
                                         'name'            => $code,
                                         'promotion_id'    => $promotion->id,
                                         'uses_per_client' => $request->filled('uses_per_client') &&
                                                              (int)$request->get('uses_per_client') >
                                                              0 ? (int)$request->get('uses_per_client') : null,
                                         'uses_per_code'   => $request->filled('uses_per_code') &&
                                                              (int)$request->get('uses_per_code') >
                                                              0 ? (int)$request->get('uses_per_code') : null,
                                         'status'          => $promotion->status
                                     ]);
    }

}
