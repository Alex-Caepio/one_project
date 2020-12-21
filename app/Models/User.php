<?php

namespace App\Models;

use App\Scopes\PublishedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 *
 * @property int id
 * @property string email
 * @property string last_name
 * @property string first_name
 * @property string account_type
 * @property string stripe_customer_id
 * @property string stripe_account_id
 * @property Carbon email_verified_at
 * @property Plan plan
 *
 * @package App\Models
 */
class User extends Authenticatable implements MustVerifyEmail {
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
        'emails_holistify_update',
        'emails_practitioner_offers',
        'email_forward_practitioners',
        'email_forward_clients',
        'email_forward_support',
        'about_my_business',
        'business_name',
        'business_address',
        'business_email',
        'public_link',
        'business_introduction',
        'date_of_birth',
        'mobile_number',
        'mobile_number',
        'business_phone_number',
        'timezone_id',
        'avatar_url',
        'background_url',
        'termination_message'
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
    ];

    public function services() {
        return $this->hasMany(Service::class);
    }

    public function timezone() {
        return $this->belongsTo(Timezone::class);
    }

    public function articles() {
        return $this->hasMany(Article::class);
    }

    public function schedules() {
        return $this->belongsToMany(Schedule::class);
    }

    public function disciplines() {
        return $this->belongsToMany(Discipline::class, 'discipline_practitioner', 'practitioner_id', 'discipline_id')
                    ->published()->withTimeStamps();
    }

    public function promotion_codes() {
        return $this->belongsToMany(PromotionCode::class, 'user_promotion_code', 'user_id', 'promotion_code_id')
                    ->withTimeStamps();
    }

    public function favourite_services() {
        return $this->belongsToMany(Service::class, 'favorites', 'user_id', 'service_id')->withTimeStamps();
    }

    public function favourite_articles() {
        return $this->belongsToMany(Article::class, 'article_favorites', 'user_id', 'article_id')->withTimeStamps();
    }

    public function favourite_practitioners() {
        return $this->belongsToMany(__CLASS__, 'practitioner_favorites', 'practitioner_id', 'user_id')
                    ->withTimeStamps();
    }

    public function plan() {
        return $this->belongsTo(Plan::class);
    }

    public function getCommission() {
        $customRate = $this->custom_rate()->where('date_from', '<', now()->toDateTimeString())
                           ->where('date_to', '>', now()->toDateTimeString())->orWhere('indefinite_period', '=', true)
                           ->first();

        return $customRate->rate ?? $this->plan->commission_on_sale;


    }

    public function custom_rate() {
        return $this->hasMany(CustomRate::class);
    }

    public function featured_focus_area() {
        return $this->belongsToMany(FocusArea::class, 'focus_area_features_user', 'user_id', 'focus_area_id');
    }

    public function featured_practitioners() {
        return $this->belongsToMany(FocusArea::class, 'focus_area_features_user', 'focus_area_id', 'user_id');
    }

    public function featured_main_pages(): BelongsToMany {
        return $this->belongsToMany(MainPage::class, 'main_page_featured_practitioner', 'user_id', 'main_page_id');
    }

    /**
     * @return bool
     */
    public function isPractitioner(): bool {
        return $this->account_type === self::ACCOUNT_PRACTITIONER;
    }

    /**
     * @return bool
     */
    public function isClient(): bool {
        return $this->account_type === self::ACCOUNT_CLIENT;
    }

    public function promotions(): BelongsToMany {
        return $this->belongsToMany(Promotion::class, 'promotion_practitioner', 'practitioner_id', 'promotion_id');
    }

    public function freezes(): HasMany {
        return $this->hasMany(ScheduleFreeze::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function instalments()
    {
        return $this->hasMany(Instalment::class);
    }
}
