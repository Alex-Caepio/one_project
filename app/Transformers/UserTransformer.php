<?php

namespace App\Transformers;

use App\Models\GoogleCalendar;
use App\Models\User;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

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
        'plan',
        'media_images',
        'media_videos',
        'service_types',
        'focus_areas',
        'keywords',
        'user_cancellations',
        'practitioner_cancellations',
        'bookings',
        'practitioner_bookings',
        'latest_articles',
        'latest_services',
        'calendar'
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
            'slug'                        => $user->slug,
            'business_introduction'       => $user->business_introduction,
            'date_of_birth'               => $user->date_of_birth,
            'mobile_country_code'         => $user->mobile_country_code,
            'mobile_number'               => $user->mobile_number,
            'business_phone_country_code' => $user->business_phone_country_code,
            'business_phone_number'       => $user->business_phone_number,
            'email_verified_at'           => $user->email_verified_at,
            'email'                       => $user->email,
            'is_admin'                    => (bool)$user->is_admin,
            'account_type'                => $user->account_type,
            'avatar_url'                  => $user->avatar_url,
            'background_url'              => $user->background_url,
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
            'business_time_zone_id'       => $user->business_time_zone_id,
            'default_payment_method'      => $user->default_payment_method,
            'default_fee_payment_method'  => $user->default_fee_payment_method,
            'address'                     => $user->address,
            'city'                        => $user->city,
            'postal_code'                 => $user->postal_code,
            'country'                     => $user->country,
            'gender'                      => $user->gender,
            'created_at'                  => $user->created_at,
            'updated_at'                  => $user->updated_at,
            'accepted_practitioner_agreement' => $user->accepted_practitioner_agreement,
            'published_at'                => $user->published_at,
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

    public function includeMediaImages(User $user): ?Collection {
        return $this->collectionOrNull($user->media_images, new MediaImageTransformer());
    }

    public function includeMediaVideos(User $user): ?Collection {
        return $this->collectionOrNull($user->media_videos, new MediaVideoTransformer());
    }

    public function includeServiceTypes(User $user): ?Collection {
        return $this->collectionOrNull($user->service_types, new ServiceTypeTransformer());
    }

    public function includeFocusAreas(User $user): ?Collection {
        return $this->collectionOrNull($user->focus_areas, new FocusAreaTransformer());
    }

    public function includeKeywords(User $user): ?Collection {
        return $this->collectionOrNull($user->keywords, new KeywordTransformer());
    }

    public function includeUserCancellations(User $user): ?Collection {
        return $this->collectionOrNull($user->user_cancellations, new CancellationTransformer());
    }

    public function includePractitionerCancellations(User $user): ?Collection {
        return $this->collectionOrNull($user->practitioner_cancellations, new CancellationTransformer());
    }

    public function includeBookings(User $user): ?Collection {
        return $this->collectionOrNull($user->bookings, new BookingTransformer());
    }

    public function includePractitionerBookings(User $user): ?Collection {
        return $this->collectionOrNull($user->practitioner_bookings, new BookingTransformer());
    }

    public function includeLatestArticles(User $user): ?Collection {
        return $this->collectionOrNull($user->latest_articles, new ArticleTransformer());
    }

    public function includeLatestServices(User $user): ?Collection {
        return $this->collectionOrNull($user->latest_services, new ServiceTransformer());
    }

    public function includeCalendar(User $user): ?Item {
        return $this->itemOrNull($user->calendar, new GoogleCalendarTransformer());
    }


}
