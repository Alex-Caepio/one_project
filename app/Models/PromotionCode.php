<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed promotion
 * @property mixed deleted_at
 * @property mixed updated_at
 * @property mixed created_at
 * @property mixed uses_per_client
 * @property mixed uses_per_code
 * @property mixed name
 * @property mixed id
 */
class PromotionCode extends Model {

    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_DISABLED = 'disabled';
    public const STATUS_COMPLETE = 'complete';
    public const STATUS_DELETED = 'deleted';

    public function users() {
        return $this->belongsToMany(User::class, 'user_promotion_code', 'user_id', 'promotion_code_id')
                    ->withTimeStamps();
    }

    public function promotion() {
        return $this->belongsTo(Promotion::class);
    }

}
