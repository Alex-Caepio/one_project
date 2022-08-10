<?php

namespace App\Providers;

use App\Services\PaymentSystem\StripeSubscriptionService;
use App\Services\PaymentSystem\StripeSubscriptionTransferDecorator;
use App\Services\PaymentSystem\SubscriptionServiceInterface;
use Illuminate\Support\ServiceProvider;

class PaymentSystemServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(SubscriptionServiceInterface::class, function ($app) {
            return $app->makeWith(StripeSubscriptionTransferDecorator::class, [
                'service' => $app->make(StripeSubscriptionService::class),
            ]);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
