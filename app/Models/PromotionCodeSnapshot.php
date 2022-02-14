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
class PromotionCodeSnapshot extends PromotionCode
{
    protected $fillable = [
        'name',
        'status',
        'uses_per_code',
        'uses_per_client',
        'promotion_id',
        'promotion_snapshot_id',
        'promotion_code_id'
    ];

    public function promotionCode()
    {
        return $this->belongsTo(PromotionCode::class);
    }
}
