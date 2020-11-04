<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Discipline;
use App\Models\Service;
use App\Observers\ArticleObserver;
use App\ScarryClass;
use App\FakeStripeClient;
use Stripe\StripeClient;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        $this->app->instance(StripeClient::class, new StripeClient(env('STRIPE_SECRET')));

        Relation::morphMap([
                               'service'    => Service::class,
                               'discipline' => Discipline::class,
                               'article'    => Article::class,
                           ]);

        Article::observe(ArticleObserver::class);
    }
}
