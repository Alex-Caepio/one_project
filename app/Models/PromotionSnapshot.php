<?php

namespace App\Models;

/**
 * Class PromotionSnapshot
 *
 * @property mixed practitioners
 * @property mixed service_type
 * @property mixed focus_areas
 * @property mixed disciplines
 * @property mixed promotion_codes
 * @property mixed status
 * @property mixed deleted_at
 * @property mixed updated_at
 * @property mixed created_at
 * @property mixed service_type_id
 * @property mixed discount_value
 * @property mixed discount_type
 * @property mixed spend_max
 * @property mixed spend_min
 * @property mixed expiry_date
 * @property mixed valid_from
 * @property mixed name
 * @property mixed id
 * @property mixed promotion_id
 */
class PromotionSnapshot extends Promotion {

    protected $guarded = [];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
}
