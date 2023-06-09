<?php

namespace App\Models;

use App\Scopes\PublishedScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $email
 * @property string $last_name
 * @property string $first_name
 * @property string $account_type
 * @property int $timezone_id
 * @property int $business_time_zone_id
 * @property string $stripe_account_id
 * @property string $stripe_customer_id
 * @property string $default_payment_method
 * @property string $default_fee_payment_method
 * @property Carbon $email_verified_at
 * @property-read Plan $plan
 * @property-read Collection $latest_services
 * @property-read Collection $latest_articles
 * @property-read PractitionerSubscriptionDiscount|null $practitionerSubscriptionDiscount
 * @property-read Timezone $timezone Business'es timezone.
 * @property-read Timezone $user_timezone User's timezone.
 * @property-read string $full_name
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasApiTokens, HasFactory, PublishedScope, SoftDeletes;

    public const ACCOUNT_PRACTITIONER = 'practitioner';
    public const ACCOUNT_CLIENT = 'client';

    public const STATUS_REGISTERED = 'registered';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_CLOSED = 'closed';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'password',
        'about_me',
        'email',
        'emails_holistify_update',
        'emails_practitioner_offers',
        'email_forward_practitioners',
        'email_forward_clients',
        'email_forward_support',
        'about_my_business',
        'business_name',
        'business_address',
        'business_email',
        'slug',
        'business_introduction',
        'date_of_birth',
        'mobile_number',
        'mobile_country_code',
        'business_phone_number',
        'business_phone_country_code',
        'avatar_url',
        'background_url',
        'termination_message',
        'business_country_id',
        'business_city',
        'business_postal_code',
        'business_time_zone',
        'business_vat',
        'business_company_houses_id',
        'address',
        'city',
        'postal_code',
        'country_id',
        'gender',
        'accepted_practitioner_agreement',
        'accepted_client_agreement',
        'business_published_at',
        'connected_at',
        'timezone_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'datetime',
        'published_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'timezone_id' => 16,// London +0
    ];

    public function services(): HasMany
    {
        return $this->hasMany(Service::class)->published();
    }

    public function user_timezone(): BelongsTo
    {
        return $this->belongsTo(Timezone::class, 'timezone_id');
    }

    public function timezone(): BelongsTo
    {
        return $this->belongsTo(Timezone::class, 'business_time_zone_id');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(Schedule::class);
    }

    public function disciplines()
    {
        return $this
            ->belongsToMany(
                Discipline::class,
                'discipline_practitioner',
                'practitioner_id',
                'discipline_id'
            )
            ->published()
            ->withTimeStamps();
    }

    public function promotion_codes(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                PromotionCode::class,
                'user_promotion_code',
                'user_id',
                'promotion_code_id'
            )
            ->withTimeStamps();
    }

    public function favourite_services(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                Service::class,
                'favorites',
                'user_id',
                'service_id'
            )
            ->withTimeStamps();
    }

    public function favourite_articles(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                Article::class,
                'article_favorites',
                'user_id',
                'article_id'
            )
            ->withTimeStamps();
    }

    public function favourite_practitioners(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                __CLASS__,
                'practitioner_favorites',
                'practitioner_id',
                'user_id'
            )
            ->withTimeStamps();
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function getCommission()
    {
        $rate = PractitionerCommission::query()
            ->where('practitioner_id', $this->id)
            ->where(function (Builder $builder) {
                return $builder
                    ->whereRaw('(is_dateless = 0 AND date_from <= DATE(NOW()) AND date_to >= DATE(NOW()))')
                    ->orWhere(function (Builder $builder) {
                        return $builder
                            ->where('is_dateless', '=', 1);
                    });
            })
            ->min('rate');

        return $rate ?? $this->plan->commission_on_sale;
    }

    public function featured_focus_area(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                FocusArea::class,
                'focus_area_features_user',
                'user_id',
                'focus_area_id'
            );
    }

    public function focus_areas(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                FocusArea::class,
                'focus_area_user',
                'user_id',
                'focus_area_id'
            );
    }

    public function featured_practitioners(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                FocusArea::class,
                'focus_area_features_user',
                'focus_area_id',
                'user_id'
            );
    }

    public function featured_main_pages(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                MainPage::class,
                'main_page_featured_practitioner',
                'user_id',
                'main_page_id'
            );
    }

    public function isPractitioner(): bool
    {
        return $this->account_type === self::ACCOUNT_PRACTITIONER;
    }

    public function isClient(): bool
    {
        return $this->account_type === self::ACCOUNT_CLIENT;
    }

    public static function getAccountTypes(): array
    {
        return [
            self::ACCOUNT_CLIENT,
            self::ACCOUNT_PRACTITIONER,
        ];
    }

    public function promotions(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                Promotion::class,
                'promotion_practitioner',
                'practitioner_id',
                'promotion_id'
            );
    }

    public function freezes(): HasMany
    {
        return $this->hasMany(ScheduleFreeze::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function practitioner_bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'practitioner_id', 'id');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function instalments(): HasMany
    {
        return $this->hasMany(Instalment::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    public function media_images(): MorphMany
    {
        return $this->morphMany(MediaImage::class, 'morphesTo', 'model_name', 'model_id');
    }

    public function media_videos(): MorphMany
    {
        return $this->morphMany(MediaVideo::class, 'morphesTo', 'model_name', 'model_id');
    }

    public function service_types(): BelongsToMany
    {
        return $this->belongsToMany(ServiceType::class, 'service_type_user', 'user_id', 'service_type_id');
    }

    public function keywords(): belongsToMany
    {
        return $this->belongsToMany(Keyword::class, 'keyword_user', 'user_id', 'keyword_id');
    }

    public function user_cancellations(): HasMany
    {
        return $this->hasMany(Cancellation::class, 'id', 'user_id');
    }

    public function practitioner_cancellations(): HasMany
    {
        return $this->hasMany(Cancellation::class, 'id', 'practitioner_id');
    }

    public function latest_services(): HasMany
    {
        return $this->hasMany(Service::class)
            ->where('is_published', true)
            ->orderBy('created_at', 'desc');
    }

    public function latest_articles(): HasMany
    {
        return $this->hasMany(Article::class)
            ->where('is_published', true)
            ->orderBy('published_at', 'desc');
    }

    public function calendar(): HasOne
    {
        return $this->hasOne(GoogleCalendar::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function business_country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'business_country_id');
    }

    public function unavailabilities(): HasMany
    {
        return $this->hasMany(UserUnavailabilities::class, 'practitioner_id', 'id');
    }


    public function isFullyRestricted(): bool
    {
        return !$this->is_admin && !$this->plan;
    }

    public function onlyUnpublishedAllowed(): bool
    {
        return $this->is_admin || ($this->account_type === User::ACCOUNT_PRACTITIONER && $this->plan);
    }

    public function generalTimezone(): HasOne
    {
        return $this->hasOne(Timezone::class, 'id', 'timezone_id');
    }

    public function practitionerSubscriptionDiscount(): HasOne
    {
        return $this->hasOne(PractitionerSubscriptionDiscount::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function canPublishArticle(): bool
    {
        if ($this->is_admin) {
            return true;
        }

        if ($this->isFullyRestricted()) {
            return false;
        }

        return $this->plan->isAllowedToPublishForNumberOfArticles($this->articles()->published()->count());
    }
}
