<?php


namespace App\Helpers;

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
    public static function userAllowAddSchedule(User $user, Service $service): bool {

        if ($user->is_admin) {
            return true;
        }

        if ($user->isFullyRestricted()) {
            return false;
        }

        if ($user->plan->schedules_per_service_unlimited || !$service->is_published) {
            return true;
        }

        return $service->schedules()->count() < (int)$user->plan->schedules_per_service;
    }

    /**
     * @param \App\Models\User $user
     * @param int $cntAttendies
     * @return bool
     */
    public static function userAllowAttendees(User $user, int $cntAttendies): bool {

        if ($user->is_admin) {
            return true;
        }

        if ($user->isFullyRestricted()) {
            return false;
        }

        return $user->plan->unlimited_bookings || $cntAttendies < $user->plan->amount_bookings;
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

    public static function unpublishPractitioner(): void {
        // unpublish all
    }

    public static function downgradePractitioner(User $user): void {
        // downgrade all
        /*
        $user->refresh();

        if (!$user->plan->article_publishing) {
            Article::where('user_id', $user->id)->update(['is_published' => false]);
        }
        */
    }

}
