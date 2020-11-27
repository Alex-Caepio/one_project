<?php


namespace App\Transformers;

use App\Models\Promotion;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class PromotionTransformer extends Transformer {

    /**
     * @var string[]
     */
    protected $availableIncludes = [
        'promotion_codes',
        'disciplines',
        'focus_areas',
        'service_type',
        'practitioners'
    ];


    /**
     * @param \App\Models\Promotion $promotion
     * @return array
     */
    public function transform(Promotion $promotion): array {
        return [
            'id'              => $promotion->id,
            'name'            => $promotion->name,
            'valid_from'      => $this->dateTime($promotion->valid_from),
            'expiry_date'     => $this->dateTime($promotion->expiry_date),
            'spend_min'       => $promotion->spend_min,
            'spend_max'       => $promotion->spend_max,
            'discount_type'   => $promotion->discount_type,
            'discount_value'  => $promotion->discount_value,
            'service_type_id' => $promotion->service_type_id,
            'created_at'      => $this->dateTime($promotion->created_at),
            'updated_at'      => $this->dateTime($promotion->updated_at),
            'deleted_at'      => $this->dateTime($promotion->deleted_at),
            'status'          => $promotion->status,
            'applied_to'      => $promotion->applied_to,
        ];
    }

    /**
     * @param \App\Models\Promotion $promotion
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includePromotionCodes(Promotion $promotion): ?Collection {
        return $this->collectionOrNull($promotion->promotion_codes, new PromotionCodeTransformer());
    }

    /**
     * @param \App\Models\Promotion $promotion
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeDisciplines(Promotion $promotion): ?Collection {
        return $this->collectionOrNull($promotion->disciplines, new DisciplineTransformer());
    }

    /**
     * @param \App\Models\Promotion $promotion
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeFocusAreas(Promotion $promotion): ?Collection {
        return $this->collectionOrNull($promotion->focus_areas, new FocusAreaTransformer());
    }

    /**
     * @param \App\Models\Promotion $promotion
     * @return \League\Fractal\Resource\Item
     */
    public function includeServiceType(Promotion $promotion): ?Item {
        return $this->itemOrNull($promotion->service_type, new ServiceTypeTransformer());
    }

    /**
     * @param \App\Models\Promotion $promotion
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includePractitioners(Promotion $promotion): ?Collection {
        return $this->collectionOrNull($promotion->practitioners, new UserTransformer());
    }
}
