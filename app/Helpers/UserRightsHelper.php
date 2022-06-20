<?php


namespace App\Helpers;

use App\Actions\Cancellation\CancelBooking;
use App\Models\Article;
use App\Models\Booking;
use App\Models\Plan;
use App\Models\Schedule;
use App\Models\ScheduleFreeze;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Exception;
use Illuminate\Support\Facades\Log;

class UserRightsHelper
{


    /**
     * @param User $user
     * @return bool
     */
    public static function userAllowToPublishArticle(User $user): bool
    {
        if ($user->is_admin) {
            return true;
        }

        if ($user->isFullyRestricted()) {
            return false;
        }

        return $user->plan->article_publishing_unlimited ||
            $user->articles()->published()->count() <= (int)$user->plan->article_publishing;
    }

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

    public static function unpublishPractitioner(User $user, bool $unpublishOnDelete = false): void
    {
        self::unpublishArticles($user);
        self::unpublishService($user);

        $requestFlag = request('cancel_bookings', false);
        if ($requestFlag === true || $unpublishOnDelete === true) {
            $bookings = Booking::query()
                ->where('practitioner_id', $user->id)
                ->where('status', 'upcoming')
                ->whereHas('schedule', function (Builder $query) {
                    $query->whereHas('service', function (Builder $query) {
                        $query->whereIn('services.service_type_id',[
                            Service::TYPE_EVENT,
                            Service::TYPE_WORKSHOP,
                            Service::TYPE_RETREAT,
                            Service::TYPE_BESPOKE,
                        ]);
                        $query->orWhere(function(Builder $query) {
                            $query->where([
                                ['services.service_type_id', '=', Service::TYPE_APPOINTMENT],
                                ['bookings.datetime_from', '>', date('Y-m-d H:i:s')],
                            ]);
                        });
                    });
                })->active()
                ->get();

            foreach ($bookings as $booking) {
                try {
                    run_action(CancelBooking::class, $booking, false, User::ACCOUNT_PRACTITIONER);
                } catch (Exception $e) {
                    Log::channel('practitioner_cancel_error')->info('[[Cancellation on unpublish failed]]: ', [
                        'user_id' => $booking->user_id ?? null,
                        'practitioner_id' => $booking->practitioner_id ?? null,
                        'booking_id' => $booking->id ?? null,
                        'message' => $e->getMessage(),
                    ]);
                }
            }
        }
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
            self::unpublishService($user, $allowedServiceTypes);
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
                    'plan_articles_cnt' => (int)$plan->article_publishing,
                ]);

            if ($existingArticles > (int)$plan->article_publishing) {
                $limit = $existingArticles - (int)$plan->article_publishing;
                $articlesId = $user
                    ->articles()
                    ->published()
                    ->orderBy('created_at', 'asc')
                    ->limit($limit)
                    ->pluck('id')
                    ->toArray();
                self::unpublishArticles($user, $articlesId);
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
                            ->toArray();
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

    public static function unpublishArticles(User $user, array $articlesId = []): void
    {
        $articleQuery = Article::where('user_id', $user->id);
        if (count($articlesId)) {
            $articleQuery->whereIn('id', $articlesId);
        }
        $articleQuery->update(['is_published' => false]);
    }

    public static function unpublishService(User $user, array $allowedServiceTypes = []): void
    {
        $serviceQuery = Service::where('user_id', $user->id);
        if (count($allowedServiceTypes)) {
            $serviceQuery->whereNotIn('services.service_type_id', $allowedServiceTypes);
        }
        $ids = $serviceQuery->pluck('services.id')->toArray();
        Schedule::whereIn('service_id', $ids)->update(['is_published' => false]);
        ScheduleFreeze::whereIn('schedule_id', $ids)->delete();
        $serviceQuery->update(['is_published' => false]);
    }

}
