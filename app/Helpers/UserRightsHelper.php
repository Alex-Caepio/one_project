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

        if ($user->isFullyRestricted()) {
            return false;
        }

        return $user->plan->article_publishing_unlimited
               || $user->articles()->published()->count() < (int)$user->plan->article_publishing;
    }

    /**
     * @param \App\Models\User $user
     * @return bool
     */
    public static function userAllowFreeSchedule(User $user): bool {
        if ($user->is_admin) {
            return true;
        }

        if ($user->isFullyRestricted()) {
            return false;
        }

        return $user->plan->list_free_services;
    }

    /**
     * @param \App\Models\User $user
     * @return bool
     */
    public static function userAllowPaidSchedule(User $user): bool {
        if ($user->is_admin) {
            return true;
        }

        if ($user->isFullyRestricted()) {
            return false;
        }

        return $user->plan->list_paid_services;
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

        if ($user->isFullyRestricted()) {
            return false;
        }

        return $user->plan->pricing_options_per_service_unlimited || $pricesCnt <= (int)$user->plan->pricing_options_per_service;
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

        if ($user->isFullyRestricted()) {
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

        if ($user->isFullyRestricted()) {
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

        if ($service->user->isFullyRestricted()) {
            return false;
        }

        return $service->user->plan->unlimited_bookings
               || $service->user->plan->amount_bookings > Booking::where('practitioner_id', $service->user->id)->count();
    }

    public static function unpublishPractitioner(): void {
        // unpublish all
    }

    public static function downgradePractitioner(): void {
        // downgrade all
    }

}
