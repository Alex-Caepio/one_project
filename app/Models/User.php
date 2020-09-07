<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
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
 * @property Carbon email_verified_at
 *
 * @package App\Models
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'password',
        'about_me',
        'emails_holistify_update',
        'emails_practitioner_offers',
        'email_forvard_practitioners',
        'email_forvard_clients',
        'email_forvard_support',
        'about_my_busines',
        'busines_name',
        'busines_address',
        'busines_email',
        'public_link',
        'busines_introduction',
        'date_of_birth',
        'mobile_number',
        'mobile_number',
        'busines_phone_number',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
    public function schedules()
    {
        return $this->belongsToMany(Schedule::class);
    }
    public function disciplines()
    {
        return $this->belongsToMany(Discipline::class,'discipline_practitioner','discipline_id','practitioner_id')->withTimeStamps();
    }
    public function promotion_codes()
    {
        return $this->belongsToMany(PromotionCode::class,'user_promotion_code','user_id','promotion_code_id')->withTimeStamps();
    }
    public function favourite_services()
    {
        return $this->belongsToMany(Service::class, 'favorites', 'user_id', 'service_id')->withTimeStamps();
    }

    public function favourite_articles()
    {
        return $this->belongsToMany(Article::class, 'article_favorites', 'user_id', 'article_id')->withTimeStamps();
    }

    public function favourite_practitioners()
    {
        return $this->belongsToMany(User::class, 'practitioner_favorites', 'user_id', 'practitioner_id')->withTimeStamps();
    }


}
