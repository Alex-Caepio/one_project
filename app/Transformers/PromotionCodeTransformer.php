<?php


namespace App\Transformers;

use App\Models\Promotion;
use App\Models\PromotionCode;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class PromotionCodeTransformer extends Transformer {

    /**
     * @var string[]
     */
    protected $availableIncludes = [
        'promotion',
        'users',
    ];

    public function transform(PromotionCode $promotionCode): array {
        return [
            'id'              => $promotionCode->id,
            'name'            => $promotionCode->name,
            'uses_per_code'   => $promotionCode->uses_per_code,
            'uses_per_client' => $promotionCode->uses_per_client,
            'created_at'      => $promotionCode->created_at,
            'updated_at'      => $promotionCode->updated_at,
            'deleted_at'      => $promotionCode->deleted_at
        ];
    }

    /**
     * @param \App\Models\PromotionCode $promotionCode
     * @return \League\Fractal\Resource\Item
     */
    public function includePromotion(PromotionCode $promotionCode): Item {
        return $this->itemOrNull($promotionCode->promotion, new PromotionTransformer());
    }

    /**
     * @param \App\Models\PromotionCode $promotionCode
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includePractitioners(PromotionCode $promotionCode): ?Collection {
        return $this->collectionOrNull($promotionCode->users, new UserTransformer());
    }
}
