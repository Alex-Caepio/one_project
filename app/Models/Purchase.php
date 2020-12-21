<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory;
    use softDeletes;

    protected $fillable = [
        'user_id',
        'schedule_id',
        'price_id',
        'promocode',
        'price_original',
        'price',
        'created_at',
        'updated_at',
        'deleted_at',
        'is_deposit',
        'deposit_amount'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function price()
    {
        return $this->belongsTo(Price::class);
    }

    public function scopeFilter(Builder $builder, QueryFilter $filters)
    {
        return $filters->apply($builder);
    }
}
