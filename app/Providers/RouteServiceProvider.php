<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Booking;
use App\Models\Discipline;
use App\Models\FocusArea;
use App\Models\Promotion;
use App\Models\RescheduleRequest;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        Route::bind('publicArticle', function ($value) {
            return Article::published()->where('id', (int)$value)->orWhere('slug', (string)$value)
                ->whereHas('user', function ($query) {
                    $query->published();
                })->firstOrFail();
        });

        Route::bind('publicService', function ($value) {
            $service = Service::published();

            $service->where('slug', (string)$value)
                ->whereHas('user', function ($query) {
                    $query->published();
                });

            if (strcmp(intval($value), $value) === 0) {
                $service->orWhere('id', $value);
            }

            return $service->firstOrFail();
        });

        Route::bind('service', function ($value) {
            return Service::where('id', $value)->orWhere('slug', $value)->firstOrFail();
        });

        Route::bind('promotionWithTrashed', function ($value) {
            return Promotion::withTrashed()->where('id', (int)$value)->firstOrFail();
        });

        Route::bind('booking', function ($value) {
            return Booking::where(function ($query) use ($value) {
                $query->where('id', $value)->orWhere('reference', 'LIKE', $value);
            })->firstOrFail();
        });

        Route::bind('reschedule_request', function ($value) {
            return RescheduleRequest::query()
                ->where('id', $value)
                ->whereIn('requested_by', RescheduleRequest::getPractitionerRequestValues())
                ->firstOrFail();
        });

        Route::bind('focusArea', function ($value) {
            return FocusArea::where('id', $value)->orWhere('slug', $value)->firstOrFail();
        });

        Route::bind('discipline', function ($value) {
            return Discipline::where('id', $value)->orWhere('slug', $value)->firstOrFail();
        });

        Route::bind('user', function ($value) {
            return User::where('id', $value)->orWhere('slug', $value)->firstOrFail();
        });

        Route::bind('practitioner', function ($value) {
            return User::where('id', $value)->orWhere('slug', $value)->firstOrFail();
        });


        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapAdminRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    protected function mapAdminRoutes()
    {
        Route::prefix('api/admin')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/admin.php'));
    }
}
