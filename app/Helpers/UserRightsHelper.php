<?php


namespace App\Helpers;

use App\Actions\Cancellation\CancelBooking;
use App\Http\Requests\Auth\UnpublishPractitionerRequest;
use App\Models\Article;
use App\Models\Booking;
use App\Models\Plan;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Log;

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

        return $user->plan->article_publishing_unlimited ||
               $user->articles()->published()->count() < (int)$user->plan->article_publishing;
    }

    /**
     * @param \App\Models\User $user
     * @return bool
     */
    public static function userAllowFreeSchedule(User $user): bool {
        if ($user->is_admin) {
            return true;
        }

        if (!$user->onlyUnpublishedAllowed()) {
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

        if (!$user->onlyUnpublishedAllowed()) {
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

        if (!$user->onlyUnpublishedAllowed()) {
            return false;
        }

        return $user->plan->pricing_options_per_service_unlimited ||
               $pricesCnt <= (int)$user->plan->pricing_options_per_service;
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

        if (!$user->onlyUnpublishedAllowed()) {
            return false;
        }

        if ($user->plan->schedules_per_service_unlimited) {
            return true;
        }

        return $service->schedules()->count() < (int)$user->plan->schedules_per_service;
    }

    /**
     * @param \App\Models\User $user
     * @param int|null $cntAttendies
     * @return bool
     */
    public static function userAllowAttendees(User $user, ?int $cntAttendies): bool {

        if ($cntAttendies === null || $user->is_admin) {
            return true;
        }

        if (!$user->onlyUnpublishedAllowed()) {
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

        if (!$user->onlyUnpublishedAllowed()) {
            return false;
        }

        $serviceTypes = $user->plan->service_types()->pluck('service_types.id')->all();
        return !count($serviceTypes) || in_array($service->service_type_id, $serviceTypes, true);
    }

    public static function unpublishPractitioner(UnpublishPractitionerRequest $request, User $user): void {
        self::unpublishArticles($user);
        self::unpublishService($user);
        if ($request->getBoolFromRequest('cancel_bookings') === true) {
            $bookings = Booking::where('user_id', $user->id)->active()->get();
            foreach ($bookings as $booking) {
                try {
                    run_action(CancelBooking::class, $booking);
                } catch (\Exception $e) {
                    Log::info('[[Cancellation on unpublish failed]]: ' . $e->getMessage(),
                              ['practitioner_id' => $user->id, 'booking_id' => $booking->id]);
                }
            }
        }
    }


    public static function downgradePractitioner(User $user, Plan $plan, ?Plan $previousPlan = null): void {
        // new plan has limited types of services
        $allowedServiceTypes = $plan->service_types()->pluck('service_types.id')->all();
        if (count($allowedServiceTypes)) {
            self::unpublishService($user, $allowedServiceTypes);
        }

        // limited articles
        if (!$plan->article_publishing_unlimited) {
            $existingArticles = $user->articles()->published()->count();
            if ($existingArticles > (int)$plan->article_publishing) {
                $limit = $existingArticles - (int)$plan->article_publishing;
                $articlesId =
                    $user->articles()->published()->orderBy('created_at', 'asc')->limit($limit)->pluck('articles.id')
                         ->all();
                self::unpublishArticles($user, $articlesId);
            }
        }

        // limited schedules and prices
        $services = $user->services()->published()->get();
        foreach ($services as $service) {
            if (!$plan->schedules_per_service_unlimited) {
                $publishedSchedules = $service->schedules()->published()->get();
                if (count($publishedSchedules) > $plan->schedules_per_service) {
                    $limit = $publishedSchedules - $plan->schedules_per_service;
                    $schedulesToUnpublish =
                        $service->schedules()->published()->orderBy('start_date', 'desc')->limit($limit)
                                ->pluck('schedules.id')->all();
                    Schedule::whereIn('id', $schedulesToUnpublish)->update(['is_published' => false]);
                }
            }
            // check last published schedule for the price options
            foreach ($service->schedules()->published()->get() as $schedule) {
                if (!$plan->list_free_services) {
                    $schedule->prices()->where('is_free', true)->delete();
                }
                if (!$plan->list_paid_services) {
                    $schedule->prices()->where('is_free', false)->delete();
                }
                $pricesCnt = $schedule->prices()->count();
                if (!$user->plan->pricing_options_per_service_unlimited &&
                    $pricesCnt > (int)$user->plan->pricing_options_per_service) {
                    $limit = $pricesCnt - (int)$user->plan->pricing_options_per_service;
                    $schedule->prices()->orderBy('cost', 'asc')->limit($limit)->delete();
                }
            }
        }
    }

    public static function unpublishArticles(User $user, array $articlesId = []): void {
        $articleQuery = Article::where('user_id', $user->id);
        if (count($articlesId)) {
            $articleQuery->whereIn('id', $articlesId);
        }
        $articleQuery->update(['is_published' => false]);
    }

    public static function unpublishService(User $user, array $allowedServiceTypes = []): void {
        $serviceQuery = Service::where('user_id', $user->id);
        if (count($allowedServiceTypes)) {
            $serviceQuery->whereNotIn('services.service_type_id', $allowedServiceTypes);
        }
        $ids = $serviceQuery->pluck('services.id');
        Schedule::whereIn('service_id', $ids)->update(['is_published' => false]);
        $serviceQuery->update(['is_published' => false]);
    }

}
