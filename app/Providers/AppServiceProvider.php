<?php

namespace App\Providers;

use App\Http\Requests\Schedule\CreateScheduleInterface;
use App\Http\Requests\Schedule\AppointmentScheduleRequest;
use App\Http\Requests\Schedule\ClassAdHocScheduleRequest;
use App\Http\Requests\Schedule\ClassScheduleRequest;
use App\Http\Requests\Schedule\BespokeProgramScheduleRequest;
use App\Http\Requests\Schedule\EcontentScheduleRequest;
use App\Http\Requests\Schedule\EventScheduleRequest;
use App\Http\Requests\Schedule\ProductScheduleRequest;
use App\Http\Requests\Schedule\RetreatScheduleRequest;
use App\Http\Requests\Schedule\TrainingProgramScheduleRequest;
use App\Http\Requests\Schedule\WorkshopScheduleRequest;
use App\Models\Article;
use App\Models\Booking;
use App\Models\Discipline;
use App\Models\Price;
use App\Models\Promotion;
use App\Models\PromotionCode;
use App\Models\Purchase;
use App\Models\RescheduleRequest;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use App\Observers\ArticleObserver;
use App\Observers\BookingObserver;
use App\Observers\PriceObserver;
use App\Observers\PromotionCodeObserver;
use App\Observers\PromotionObserver;
use App\Observers\PurchaseObserver;
use App\Observers\RescheduleRequestObserver;
use App\Observers\ScheduleObserver;
use App\Observers\ServiceObserver;
use App\Observers\UserObserver;
use Stripe\StripeClient;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (! $this->app->environment('production')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->instance(StripeClient::class, new StripeClient(config('services.stripe.secret')));

        Relation::morphMap([
            'service' => Service::class,
            'discipline' => Discipline::class,
            'article' => Article::class,
            'user' => User::class,
        ]);

        /* Events Observer */
        Article::observe(ArticleObserver::class);
        Promotion::observe(PromotionObserver::class);
        PromotionCode::observe(PromotionCodeObserver::class);
        User::observe(UserObserver::class);
        Service::observe(ServiceObserver::class);
        Booking::observe(BookingObserver::class);
        Purchase::observe(PurchaseObserver::class);
        Schedule::observe(ScheduleObserver::class);
        RescheduleRequest::observe(RescheduleRequestObserver::class);
        Price::observe(PriceObserver::class);

        $this->app->bind(CreateScheduleInterface::class, function () {
            $serviceType = null;
            if (request()->service) {
                $serviceType = request()->service->service_type_id;
            } elseif (request()->schedule) {
                $serviceType = request()->schedule->service->service_type_id;
            }

            switch ($serviceType) {
                case Service::TYPE_WORKSHOP:
                    return new WorkshopScheduleRequest();

                case 'econtent':
                    return new EcontentScheduleRequest();

                case 'class_ad_hoc':
                    return new ClassAdHocScheduleRequest();

                case 'class':
                    return new ClassScheduleRequest();

                case Service::TYPE_BESPOKE:
                    return new BespokeProgramScheduleRequest();

                case Service::TYPE_EVENT:
                    return new EventScheduleRequest();

                case 'product':
                    return new ProductScheduleRequest();

                case Service::TYPE_RETREAT:
                    return new RetreatScheduleRequest();

                case 'training_program':
                    return new TrainingProgramScheduleRequest();

                case Service::TYPE_APPOINTMENT:
                    return new AppointmentScheduleRequest();

                default:
                    abort(
                        500,
                        'You\'re trying to purchase unrecognized service type. Please contact site administrator for assistance'
                    );
            }
        });

        // Force assets loading via https
        if(! $this->app->environment('local')) {
            \URL::forceScheme('https');
        }
    }
}
