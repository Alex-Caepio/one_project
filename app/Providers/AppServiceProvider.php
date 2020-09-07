<?php

namespace App\Providers;

use App\FakeStripeClient;
use App\ScarryClass;
use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;

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
        $this->app->instance(
            StripeClient::class,
            new StripeClient(env('STRIPE_SECRET'))
        );
    }
}
