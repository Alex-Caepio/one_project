<?php

use App\Providers\FilesystemNoLockFixServiceProvider;

return [
    /*
    |--------------------------------------------------------------------------
    | Custom variables
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */
    'platform_name' => env('PLATFORM_NAME', 'Oneness'),
    'platform_email' => env('PLATFORM_EMAIL', 'hello@holistify.me'),
    'platform_cancellation_fee' => env('PLATFORM_CANCELLATION_FEE', 3),
    'platform_currency' => env('PLATFORM_CURRENCY', 'gbp'),
    'platform_calendar' => env('PLATFORM_CALENDAR_NAME', 'Oneness Holistify'),
    'platform_default_timezone' => env('PLATFORM_CALENDAR_DEFAULT_TIMEZONE', null),
    'platform_subject_practitioner' => env('PLATFORM_SUBJECT_PREFIX_PRACTITIONER', 'Message from: '),
    'platform_subject_client' => env('PLATFORM_SUBJECT_PREFIX_CLIENT', 'Message from: '),
    'platform_currency_sign' => env('PLATFORM_CURRENCY_SIGN', '&pound;'),

    'dateless_service_types' => ['bespoke'],


    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool)env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),
    'frontend_url' => env('FRONTEND_URL', 'http://localhost'),
    'frontend_stripe_account_refresh' => env('FRONTEND_STRIPE_ACCOUNT_REFRESH', '/'),
    'frontend_stripe_account_redirect_back' => env('FRONTEND_STRIPE_ACCOUNT_REDIRECT_BACK', '/'),
    'frontend_password_reset_link' => env('PASSWORD_RESET_FRONTEND_LINK', '/'),
    'frontend_reset_password_form_url' => env('FRONTEND_URL_RESET_PASSWORD_FORM', '/'),
    'frontend_email_confirm_page' => env('FRONTEND_EMAIL_CONFIRM_PAGE', '/verify-email'),
    'frontend_profile_link' => env('FRONTEND_PROFILE_LINK', '/account/personal-details'),
    'frontend_account_link' => env('FRONTEND_ACCOUNT_LINK', '/account/personal-details'),
    'frontend_practitioner_services' => env('FRONTEND_PRACTITIONER_SERVICES', '/services'),
    'frontend_practitioner_articles' => env('FRONTEND_PRACTITIONER_ARTICLES', '/articles'),
    'frontend_practitioner_article_url' => env('FRONTEND_PRACTITIONER_ARTICLE_URL', '/biz/practitioner-slug/article/article-slug'),
    'frontend_booking_url' => env('FRONTEND_BOOKING_URL', '/bookings/services/'),
    'frontend_clients_booking_url' => env('FRONTEND_CLIENTS_BOOKING_URL', '/practitioner/services/'),
    'frontend_client_purchase_url' => env('FRONTEND_CLIENT_PURCHASE_URL', '/practitioner/purchase/'),
    'frontend_reschedule_apply' => env('FRONTEND_RESCHEDULE_APPLY', '/accept-reschedule'),
    'frontend_reschedule_amend_decline' => env('FRONTEND_RESCHEDULE_DECLINE', '/decline-reschedule'),
    'frontend_decline_amend' => env('FRONTEND_DECLINE_AMEND', '/decline-amend'),
    'frontend_accept_amend' => env('FRONTEND_ACCEPT_AMEND', '/accept-amend'),
    'frontend_public_profile' => env('FRONTEND_PUBLIC_PROFILE', '/biz/practitioner-slug/'),
    'frontend_public_service' => env('FRONTEND_PUBLIC_SERVICE', '/biz/practitioner-slug/service/service-slug'),
    'asset_url' => env('ASSET_URL', null),
    'platform_conversation_url' => env('FRONTEND_CONVERSATION_URL', '/conversation/'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        //Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\ResponseServiceProvider::class,
        FilesystemNoLockFixServiceProvider::class,
        App\Providers\BookingServiceProvider::class,
        App\Providers\PaymentSystemServiceProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Arr' => Illuminate\Support\Arr::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Http' => Illuminate\Support\Facades\Http::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'Str' => Illuminate\Support\Str::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class
    ],

];
