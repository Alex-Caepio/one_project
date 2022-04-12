<?php

namespace App\Models;

use App\Filters\QueryFilter;
use App\Scopes\PublishedScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Service
 *
 * @property int id
 * @property int user_id
 * @property int is_published
 * @property string service_type_id
 * @property string url
 * @property string title
 * @property string string
 * @property string stripe_id
 * @property string description
 * @property string introduction
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 * @property ServiceType service_type
 */
class Service extends Model
{
    use SoftDeletes, HasFactory, PublishedScope;

    public const TYPE_APPOINTMENT = 'appointment';
    public const TYPE_BESPOKE = 'bespoke';
    public const TYPE_EVENT = 'events';
    public const TYPE_WORKSHOP = 'workshop';
    public const TYPE_RETREAT = 'retreat';

    protected $fillable = [
        'title',
        'description',
        'introduction',
        'slug',
        'service_type_id',
        'stripe_id',
        'published_at',
        'is_published'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    public function scopeFilter(Builder $builder, QueryFilter $filters)
    {
        return $filters->apply($builder);
    }

    public function media_images(): MorphMany
    {
        return $this->morphMany(MediaImage::class, 'morphesTo', 'model_name', 'model_id');
    }

    public function media_videos(): MorphMany
    {
        return $this->morphMany(MediaVideo::class, 'morphesTo', 'model_name', 'model_id');
    }

    public function media_files(): MorphMany
    {
        return $this->morphMany(MediaFile::class, 'morphesTo', 'model_name', 'model_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function practitioner()
    {
        return $this
            ->belongsTo(User::class, 'user_id', 'id')
            ->withTrashed();
    }

    public function keywords(): BelongsToMany
    {
        return $this->belongsToMany(Keyword::class);
    }

    public function disciplines()
    {
        return $this->belongsToMany(Discipline::class)->published();
    }

    public function focus_areas(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                FocusArea::class,
                'focus_area_service',
                'service_id',
                'focus_area_id'
            )
            ->withTimeStamps();
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function active_schedules(): HasMany
    {
        return $this
            ->hasMany(Schedule::class)
            ->where('is_published', true);
    }

    public function favourite_services(): BelongsToMany
    {
        return $this->belongsToMany(__CLASS__);
    }

    public function service_type(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function featured_focus_area()
    {
        return $this
            ->belongsToMany(
                FocusArea::class,
                'focus_area_featured_service',
                'service_id',
                'focus_area_id'
            );
    }

    public function featured_main_pages(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                MainPage::class,
                'main_page_featured_service',
                'service_id',
                'main_page_id'
            );
    }

    public function featured_services()
    {
        return $this
            ->belongsToMany(
                FocusArea::class,
                'focus_area_featured_service',
                'focus_area_id',
                'service_id'
            );
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class)->published();
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function isDateLess(): bool
    {
        return in_array($this->service_type_id, config('app.dateless_service_types'), true);
    }
}
