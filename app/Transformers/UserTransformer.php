<?php

namespace App\Transformers;

use App\Models\User;

class UserTransformer extends Transformer
{
    protected $availableIncludes = ['access_token', 'services', 'articles', 'schedules', 'disciplines', 'promotion_codes', 'favourite_services', 'favourite_articles', 'favourite_practitioners'];

    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'stripe_id' => $user->stripe_id,
            'is_published' => $user->is_published,
            'about_me' => $user->about_me,
            'emails_holistify_update' => $user->emails_holistify_update,
            'emails_practitioner_offers' => $user->emails_practitioner_offers,
            'email_forward_practitioners' => $user->email_forward_practitioners,
            'email_forward_clients' => $user->email_forward_clients,
            'email_forward_support' => $user->email_forward_support,
            'about_my_business' => $user->about_my_business,
            'business_name' => $user->business_name,
            'business_address' => $user->business_address,
            'business_email' => $user->business_email,
            'public_link' => $user->public_link,
            'business_introduction' => $user->business_introduction,
            'date_of_birth' => $user->date_of_birth,
            'mobile_number' => $user->mobile_number,
            'business_phone_number' => $user->business_phone_number,
            'email_verified_at' => $user->email_verified_at,
            'email' => $user->email,
            'is_admin'=>$user->is_admin,
            'account_type' => $user->account_type,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }

    public function includeAccessToken(User $user)
    {
        return $this->itemOrNull($user->currentAccessToken(), new AccessTokenTransformer());
    }

    public function includeServices(User $user)
    {
        return $this->collectionOrNull($user->services, new ServiceTransformer());
    }

    public function includeArticles(User $user)
    {
        return $this->collectionOrNull($user->articles, new ArticleTransformer());
    }

    public function includeSchedules(User $user)
    {
        return $this->collectionOrNull($user->schedules, new ScheduleTransformer());
    }

    public function includeDisciplines(User $user)
    {
        return $this->collectionOrNull($user->disciplines, new DisciplineTransformer());
    }
    public function includePromotionCodes(User $user)
    {
        return $this->collectionOrNull($user->promotion_codes, new PromotionCodeTransformer());
    }
    public function includeFavouriteServices(User $user)
    {
        return $this->collectionOrNull($user->favourite_services, new ServiceTransformer());
    }
    public function includeFavouriteArticles(User $user)
    {
        return $this->collectionOrNull($user->favourite_articles, new ArticleTransformer());
    }
    public function includeFavouritePractitioners(User $user)
    {
        return $this->collectionOrNull($user->favourite_practitioners, new DisciplineTransformer());
    }
}
