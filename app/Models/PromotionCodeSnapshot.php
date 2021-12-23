<?php

namespace App\Models;

/**
 * Class PromotionCodeSnapshot
 *
 * @property mixed promotion
 * @property mixed deleted_at
 * @property mixed updated_at
 * @property mixed created_at
 * @property mixed uses_per_client
 * @property mixed uses_per_code
 * @property mixed name
 * @property mixed id
 */
class PromotionCodeSnapshot extends PromotionCode {
    public function promotionCode()
    {
        return $this->belongsTo(PromotionCode::class);
    }
}
