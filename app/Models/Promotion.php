<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
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
 */
class Promotion extends Model {
    use HasFactory, SoftDeletes;

    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_FIXED = 'fixed';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_DISABLED = 'disabled';
    public const STATUS_COMPLETE = 'complete';
    public const STATUS_DELETED = 'deleted';
    public const STATUS_EXPIRED = 'expired';

    public const APPLIED_HOST = 'host';
    public const APPLIED_BOTH = 'both';

    public function promotion_codes() {
        return $this->hasMany(PromotionCode::class);
    }

    public function disciplines() {
        return $this->belongsToMany(Discipline::class, 'promotion_discipline', 'promotion_id', 'discipline_id')
                    ->published()->withTimeStamps();
    }

    public function focus_areas() {
        return $this->belongsToMany(FocusArea::class, 'promotion_focus_area', 'promotion_id', 'focus_area_id')
                    ->withTimeStamps();
    }

    public function service_type() {
        return $this->hasOne(ServiceType::class);
    }

    public function practitioners() {
        return $this->belongsToMany(User::class, 'promotion_practitioner', 'promotion_id', 'practitioner_id')
                    ->published()->withTimeStamps();
    }
}
