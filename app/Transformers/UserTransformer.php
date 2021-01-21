<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\Resource\Collection;

class UserTransformer extends Transformer {
    protected $availableIncludes = [
        'access_token',
        'services',
        'articles',
        'schedules',
        'disciplines',
        'promotion_codes',
        'favourite_services',
        'favourite_articles',
        'favourite_practitioners',
        'plan'
    ];

    public function transform(User $user) {
        return [
            'id'                          => $user->id,
            'first_name'                  => $user->first_name,
            'last_name'                   => $user->last_name,
            'stripe_customer_id'          => $user->stripe_customer_id,
            'is_published'                => (bool)$user->is_published,
            'about_me'                    => $user->about_me,
            'emails_holistify_update'     => (bool)$user->emails_holistify_update,
            'emails_practitioner_offers'  => (bool)$user->emails_practitioner_offers,
            'email_forward_practitioners' => (bool)$user->email_forward_practitioners,
            'email_forward_clients'       => (bool)$user->email_forward_clients,
            'email_forward_support'       => (bool)$user->email_forward_support,
            'about_my_business'           => $user->about_my_business,
            'business_name'               => $user->business_name,
            'business_address'            => $user->business_address,
            'business_email'              => $user->business_email,
            'public_link'                 => $user->public_link,
            'business_introduction'       => $user->business_introduction,
            'date_of_birth'               => $user->date_of_birth,
            'mobile_country_code'         => $user->mobile_country_code,
            'mobile_number'               => $user->mobile_number,
            'business_phone_country_code' => $user->business_phone_country_code,
            'business_phone_number'       => $user->business_phone_number,
            'email_verified_at'           => $user->email_verified_at,
            'email'                       => $user->email,
            'is_admin'                    => $user->is_admin,
            'account_type'                => $user->account_type,
            'avatar_url'                  => $user->avatar_url,
            'background_url'              => $user->background_url,
            'created_at'                  => $this->dateTime($user->created_at),
            'updated_at'                  => $this->dateTime($user->updated_at),
            'termination_message'         => $user->termination_message,
            'status'                      => $user->status,
            'business_country'            => $user->business_country,
            'business_city'               => $user->business_city,
            'business_postal_code'        => $user->business_postal_code,
            'business_time_zone'          => $user->business_time_zone,
            'business_vat'                => $user->business_vat,
            'business_company_houses_id'  => $user->business_company_houses_id,
            'plan_from'                   => $user->plan_from,
            'plan_until'                  => $user->plan_until,
            'discipline_id'               => $user->discipline_id,
        ];
    }

    public function includeAccessToken(User $user) {
        return $this->itemOrNull($user->currentAccessToken(), new AccessTokenTransformer());
    }

    public function includeServices(User $user) {
        return $this->collectionOrNull($user->services, new ServiceTransformer());
    }

    public function includeArticles(User $user) {
        return $this->collectionOrNull($user->articles, new ArticleTransformer());
    }

    public function includeSchedules(User $user) {
        return $this->collectionOrNull($user->schedules, new ScheduleTransformer());
    }

    public function includeDisciplines(User $user) {
        return $this->collectionOrNull($user->disciplines, new DisciplineTransformer());
    }

    public function includePromotionCodes(User $user) {
        return $this->collectionOrNull($user->promotion_codes, new PromotionCodeTransformer());
    }

    public function includeFavouriteServices(User $user) {
        return $this->collectionOrNull($user->favourite_services, new ServiceTransformer());
    }

    public function includeFavouriteArticles(User $user) {
        return $this->collectionOrNull($user->favourite_articles, new ArticleTransformer());
    }

    public function includeFavouritePractitioners(User $user) {
        return $this->collectionOrNull($user->favourite_practitioners, new DisciplineTransformer());
    }

    public function includePlan(User $user) {
        return $this->itemOrNull($user->plan, new PlanTransformer());
    }

    /**
     * @param \App\Models\User $user
     * @return \League\Fractal\Resource\Collection|null
     */

    public function includeMediaImages(User $user): ?Collection {
        return $this->collectionOrNull($user->media_images, new MediaImageTransformer());
    }
}
