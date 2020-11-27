<?php

namespace App\Providers;

use App\Http\Requests\Api\v2\Checkout\Interfaces\CreateScheduleInterface;
use App\Http\Requests\Schedule\AppointmentScheduleRequest;
use App\Http\Requests\Schedule\ClassAdHocScheduleRequest;
use App\Http\Requests\Schedule\ClassScheduleRequest;
use App\Http\Requests\Schedule\CourceProgramScheduleRequest;
use App\Http\Requests\Schedule\EcontentScheduleRequest;
use App\Http\Requests\Schedule\EventScheduleRequest;
use App\Http\Requests\Schedule\ProductScheduleRequest;
use App\Http\Requests\Schedule\PurchaseScheduleRequest;
use App\Http\Requests\Schedule\RetreatScheduleRequest;
use App\Http\Requests\Schedule\TrainingProgramScheduleRequest;
use App\Http\Requests\Schedule\WorkshopScheduleRequest;
use App\Models\Article;
use App\Models\Discipline;
use App\Models\Promotion;
use App\Models\Service;
use App\Observers\ArticleObserver;
use App\Observers\PromotionObserver;
use App\ScarryClass;
use App\FakeStripeClient;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
       $this->app->instance(StripeClient::class, new StripeClient(env('STRIPE_SECRET')));

        Relation::morphMap([
            'service'    => Service::class,
            'discipline' => Discipline::class,
            'article'    => Article::class,
        ]);

        /* Events Observer */
        Article::observe(ArticleObserver::class);
        Promotion::observe(PromotionObserver::class);

        $this->app->bind(CreateScheduleInterface::class, function () {
            if (request()->service->service_type['id'] == 'workshop') {
                return new WorkshopScheduleRequest();
            } else if (request()->service->service_type->id == 'econtent') {
                return new EcontentScheduleRequest();
            } else if (request()->service->service_type->id == 'class_ad_hoc') {
                return new ClassAdHocScheduleRequest();
            } else if (request()->service->service_type->id == 'class') {
                return new ClassScheduleRequest();
            } else if (request()->service->service_type->id == 'courses') {
                return new CourceProgramScheduleRequest();
            } else if (request()->service->service_type->id == 'events') {
                return new EventScheduleRequest();
            } else if (request()->service->service_type->id == 'product') {
                return new ProductScheduleRequest();
            } else if (request()->service->service_type->id == 'retreat') {
                return new RetreatScheduleRequest();
            } else if (request()->service->service_type->id == 'training_program') {
                return new TrainingProgramScheduleRequest();
            }  else if (request()->service->service_type->id == 'appointment') {
                return new AppointmentScheduleRequest();
            }
        });
    }
}
