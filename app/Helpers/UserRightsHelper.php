<?php

namespace App\Helpers;

use App\Actions\Article\UnpublishArticles;
use App\Actions\Service\UnpublishServices;
use App\Models\Plan;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserRightsHelper
{
    /**
     * @param User $user
     * @return bool
     */
    public static function userAllowFreeSchedule(User $user): bool
    {
        if ($user->is_admin) {
            return true;
        }

        if (!$user->onlyUnpublishedAllowed()) {
            return false;
        }

        return $user->plan->list_free_services;
    }

    /**
     * @param User $user
     * @return bool
     */
    public static function userAllowPaidSchedule(User $user): bool
    {
        if ($user->is_admin) {
            return true;
        }

        if (!$user->onlyUnpublishedAllowed()) {
            return false;
        }

        return $user->plan->list_paid_services;
    }

    /**
     * @param User $user
     * @param int $pricesCnt
     * @return bool
     */
    public static function userAllowAddPriceOptions(User $user, int $pricesCnt): bool
    {
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
     * @param User $user
     * @param Service $service
     * @return bool
     */
    public static function userAllowAddSchedule(User $user, Service $service): bool
    {
        if ($user->is_admin) {
            return true;
        }

        if (!$user->onlyUnpublishedAllowed()) {
            return false;
        }

        if ($user->plan->schedules_per_service_unlimited) {
            return true;
        }

        return $service->schedules()->where('is_published', 1)->count() <= (int)$user->plan->schedules_per_service;
    }

    public static function userAllowDeposit(User $user): bool
    {
        return $user->is_admin || $user->plan->take_deposits_and_instalment;
    }

    /**
     * @param User $user
     * @param int|null $cntAttendies
     * @return bool
     */
    public static function userAllowAttendees(User $user, ?int $cntAttendies): bool
    {
        if ($cntAttendies === null || $user->is_admin) {
            return true;
        }

        if (!$user->onlyUnpublishedAllowed()) {
            return false;
        }

        return $user->plan->unlimited_bookings || $cntAttendies <= $user->plan->amount_bookings;
    }

    /**
     * @param User $user
     * @param Service $service
     * @return bool
     */
    public static function userAllowPublishService(User $user, Service $service): bool
    {
        if ($user->is_admin) {
            return true;
        }

        if (!$user->onlyUnpublishedAllowed()) {
            return false;
        }

        $serviceTypes = $user->plan->service_types()->pluck('service_types.id')->toArray();

        return !count($serviceTypes) || in_array($service->service_type_id, $serviceTypes, true);
    }

    public static function downgradePractitioner(User $user, Plan $plan, ?Plan $previousPlan = null): void
    {
        Log::channel('stripe_plans_info')
            ->info("Change practitioner plan", [
                'user_id' => $user->id,
                'new_plan_id' => $plan->id,
                'plan_name' => $plan->name,
            ]);

        // new plan has limited types of services
        $allowedServiceTypes = $plan->service_types()->pluck('service_types.id')->toArray();

        Log::channel('stripe_plans_info')
            ->info(
                "Allowed service types: ",
                array_merge([
                    'user_id' => $user->id,
                    'new_plan_id' => $plan->id,
                ], $allowedServiceTypes)
            );

        if (count($allowedServiceTypes)) {
            run_action(UnpublishServices::class, $user, $allowedServiceTypes);
        }

        // limited articles
        if (!$plan->article_publishing_unlimited) {
            $existingArticles = $user->articles()->published()->count();
            Log::channel('stripe_plans_info')
                ->info("Articles restrictions", [
                    'user_id' => $user->id,
                    'new_plan_id' => $plan->id,
                    'plan_name' => $plan->name,
                    'practitioner_articles_cnt' => $existingArticles,
                    'plan_articles_cnt' => (int) $plan->article_publishing,
                ]);

            if ($existingArticles > (int) $plan->article_publishing) {
                $limit = $existingArticles - (int) $plan->article_publishing;
                $articlesIds = $user
                    ->articles()
                    ->published()
                    ->orderBy('created_at', 'asc')
                    ->limit($limit)
                    ->pluck('id')
                    ->toArray()
                ;
                run_action(UnpublishArticles::class, $user, $articlesIds);
            }
        }

        // limited schedules and prices
        $services = $user->services()->published()->get();
        foreach ($services as $service) {
            if (!$plan->take_deposits_and_instalment) {
                Schedule::where('service_id', $service->id)->update([
                    'deposit_accepted' => 0,
                    'deposit_amount' => null,
                    'deposit_instalments' => null,
                    'deposit_instalment_frequency' => null,
                    'deposit_final_date' => null
                ]);
            }

            if (!$plan->schedules_per_service_unlimited) {
                $publishedSchedules = $service->schedules()->published()->count();

                Log::channel('stripe_plans_info')
                    ->info("Schedule restrictions", [
                        'user_id' => $user->id,
                        'new_plan_id' => $plan->id,
                        'plan_name' => $plan->name,
                        'published_cnt' => $publishedSchedules,
                        'allowed_cnt' => $plan->schedules_per_service,
                        'service_id' => $service->id,
                    ]);

                if ($publishedSchedules > $plan->schedules_per_service) {
                    $limit = $publishedSchedules - $plan->schedules_per_service;
                    $schedulesToUnpublish =
                        $service->schedules()
                        ->published()
                        ->orderBy('start_date', 'desc')
                        ->limit($limit)
                        ->pluck('schedules.id')
                        ->toArray()
                    ;
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
                if (
                    !$plan->pricing_options_per_service_unlimited
                    && $pricesCnt > (int) $plan->pricing_options_per_service
                ) {
                    $limit = $pricesCnt - (int) $plan->pricing_options_per_service;
                    $schedule->prices()->orderBy('cost', 'asc')->limit($limit)->delete();
                }
            }
        }
    }
}
