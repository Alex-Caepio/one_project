<?php


namespace App\Transformers;
use App\Models\PromotionCode;

class PromotionCodeTransformer extends Transformer
{
    public function transform(PromotionCode $promotionCode)
    {
        return [
            'id'                          => $promotionCode->id,
            'name'                        => $promotionCode->name,
            'valid_from'                  => $promotionCode->valid_from,
            'expiry_date'                 => $promotionCode->expiry_date,
            'spend_min'                   => $promotionCode->spend_min,
            'spend_max'                   => $promotionCode->spend_max,
            'discount_type'               => $promotionCode->discount_type,
            'discount_value'              => $promotionCode->discount_value,
            'service_type_id'             => $promotionCode->service_type_id,
            'discipline_id'               => $promotionCode->discipline_id,
            'focus_area_id'               => $promotionCode->focus_area_id,
            'max_uses_per_code'           => $promotionCode->max_uses_per_code,
            'code_uses_per_customer'      => $promotionCode->code_uses_per_customer,
            'created_at'                  => $promotionCode->created_at,
            'updated_at'                  => $promotionCode->updated_at,
        ];
    }
}
