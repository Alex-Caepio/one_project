<?php


namespace App\Helpers;


use App\Models\Booking;
use App\Models\Service;
use App\Models\User;

class UserRightsHelper {


    /**
     * @param \App\Models\User $user
     * @return bool
     */
    public static function userAllowToPublishArticle(User $user): bool {

        if ($user->is_admin) {
            return true;
        }

        if (!$user->plan) {
            return false;
        }

        if ($user->plan->article_publishing_unlimited) {
            return true;
        }

        return $user->articles()->published()->count() < (int)$user->plan->article_publishing;
    }

    /**
     * @param \App\Models\User $user
     * @return bool
     */
    public static function userAllowFreeSchedule(User $user): bool {
        if ($user->is_admin) {
            return true;
        }

        return $user->plan && $user->plan->list_free_services;
    }

    /**
     * @param \App\Models\User $user
     * @return bool
     */
    public static function userAllowPaidSchedule(User $user): bool {
        if ($user->is_admin) {
            return true;
        }

        return $user->plan && $user->plan->list_free_services;
    }

    /**
     * @param \App\Models\User $user
     * @param int $pricesCnt
     * @return bool
     */
    public static function userAllowAddPriceOptions(User $user, int $pricesCnt): bool {

        if ($user->is_admin) {
            return true;
        }

        if (!$user->plan) {
            return false;
        }

        if ($user->plan->pricing_options_per_service_unlimited) {
            return true;
        }

        return $pricesCnt <= (int)$user->plan->pricing_options_per_service;
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\Service $service
     * @return bool
     */
    public static function userAllowPublishSchedule(User $user, Service $service): bool {

        if ($user->is_admin) {
            return true;
        }

        if (!$user->plan) {
            return false;
        }

        if ($user->plan->schedules_per_service_unlimited || !$service->is_published) {
            return true;
        }

        return $service->schedules()->published()->count() < (int)$user->plan->schedules_per_service;
    }


    /**
     * @param \App\Models\User $user
     * @param \App\Models\Service $service
     * @return bool
     */
    public static function userAllowPublishService(User $user, Service $service): bool {

        if ($user->is_admin) {
            return true;
        }

        if (!$user->plan) {
            return false;
        }

        $serviceTypes = $user->plan->service_types()->pluck('service_types.id')->all();
        return !count($serviceTypes) || in_array($service->service_type_id, $serviceTypes, true);
    }


    /**
     * @param \App\Models\Service $service
     * @return bool
     */
    public static function userAllowToBook(Service $service): bool {

        if ($service->user->is_admin) {
            return true;
        }

        if (!$service->user->plan) {
            return false;
        }

        return $service->user->plan->unlimited_bookings
               || $service->user->plan->amount_bookings > Booking::where('practitioner_id', $service->user->id)->count();
    }



}
