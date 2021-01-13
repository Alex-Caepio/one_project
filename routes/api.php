<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MainPageController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ScheduleFreezesController;
use App\Http\Controllers\SchedulePriceController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StripeAccountController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\PractitionerController;
use App\Http\Controllers\DisciplineController;
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CardStripeController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\RescheduleRequestController;
use App\Http\Controllers\FocusAreaController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\TimezoneController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);
Route::get('auth/verify-email', [AuthController::class, 'verifyEmail'])->name('verify-email');

Route::post('auth/forgot-password-ask', [ResetPasswordController::class, 'askForReset']);
Route::post('auth/verify-forgot-password-token', [ResetPasswordController::class, 'verifyToken']);
Route::post('auth/forgot-password-claim', [ResetPasswordController::class, 'claimReset'])
    ->name('claim-reset');

/* Public Routes For Services And Articles */
Route::get('articles', [ArticleController::class, 'index']);
Route::get('articles/{publicArticle}', [ArticleController::class, 'show'])->where('publicArticle', '[0-9]+');
Route::get('services', [ServiceController::class, 'index']);
Route::get('services/{publicService}', [ServiceController::class, 'show'])->where('publicService', '[0-9]+');

Route::get('/mainpage', [MainPageController::class, 'index']);

Route::get('/plans', [PlanController::class, 'index']);

Route::get('/service-types', [ServiceTypeController::class, 'index']);

Route::middleware(['auth:sanctum', 'unsuspended'])->group(function () {
    Route::get('auth/profile', [AuthController::class, 'profile']);
    Route::put('auth/profile', [AuthController::class, 'update']);
    Route::post('auth/profile/publish', [AuthController::class, 'publish']);
    Route::post('auth/resend-verification', [AuthController::class, 'resendVerification']);
    Route::post('auth/profile/avatar', [AuthController::class, 'avatar']);
    Route::post('auth/profile/background', [AuthController::class, 'background']);

    Route::get('stripe/link', [StripeAccountController::class, 'link']);
    Route::get('stripe/account', [StripeAccountController::class, 'account']);

    Route::post('services/{service}/favourite', [ServiceController::class, 'storeFavorite']);
    Route::delete('services/{service}/favourite', [ServiceController::class, 'deleteFavorite']);
    Route::get('/services/favourites', [UserController::class, 'serviceFavorites']);

    Route::post('practitioners/{practitioner}/favourite', [PractitionerController::class, 'storeFavorite']);
    Route::delete('practitioners/{practitioner}/favourite', [PractitionerController::class, 'deleteFavorite']);
    Route::get('/practitioners/favourites', [UserController::class, 'practitionerFavorites']);

    Route::middleware(['practitioner'])->group(function () {
        Route::get('articles/practitioner', [ArticleController::class, 'practitionerArticleList']);
        Route::get('articles/practitioner/{article}', [ArticleController::class, 'practitionerArticleShow']);
        Route::post('articles', [ArticleController::class, 'store']);
        Route::put('articles/{article}', [ArticleController::class, 'update']);
        Route::delete('articles/{article}', [ArticleController::class, 'destroy']);

        Route::get('services/practitioner', [ServiceController::class, 'practitionerServiceList']);
        Route::get('services/practitioner/{service}', [ServiceController::class, 'practitionerServiceShow']);
        Route::post('services', [ServiceController::class, 'store']);
        Route::put('services/{service}', [ServiceController::class, 'update']);
        Route::post('services/{service}/publish', [ServiceController::class, 'publish']);
        Route::post('services/{service}/unpublish', [ServiceController::class, 'unpublish']);
        Route::delete('services/{service}', [ServiceController::class, 'destroy']);
    });

    Route::post('/articles/{article}/favourite', [ArticleController::class, 'storeFavorite']);
    Route::delete('/articles/{article}/favourite', [ArticleController::class, 'deleteFavorite']);
    Route::get('/articles/favourites', [UserController::class, 'articleFavorites']);

    Route::get('disciplines', [DisciplineController::class, 'index']);
    Route::get('disciplines/{discipline}', [DisciplineController::class, 'show']);

    Route::get('keywords', [KeywordController::class, 'index']);
    Route::get('keywords/list', [KeywordController::class, 'list']);
    Route::get('keywords/filter', [KeywordController::class, 'filter']);

    Route::get('locations', [LocationController::class, 'index']);
    Route::get('locations/list', [LocationController::class, 'list']);

    Route::get('practitioners', [PractitionerController::class, 'index']);
    Route::get('practitioners/list', [PractitionerController::class, 'list']);

    Route::post('/credit-cards', [CardStripeController::class, 'store']);
    Route::get('/credit-cards', [CardStripeController::class, 'index']);

    Route::get('/plans', [PlanController::class, 'index']);
    Route::post('/plans/{plan}/purchase', [PlanController::class, 'purchase']);

    Route::post('/services/{service}/schedules', [ScheduleController::class, 'store']);
    Route::get('/services/{service}/schedules', [ScheduleController::class, 'index']);
    Route::put('/schedules/{schedule}', [ScheduleController::class, 'update']);
    Route::get('/schedules/{schedule}/attendants', [ScheduleController::class, 'allUser']);
    Route::post('/schedules/{schedule}/freeze', [ScheduleController::class, 'freeze']);
    Route::get('/schedules/{schedule}/availabilities', [ScheduleController::class, 'availabilities']);
    Route::post('/schedules/{schedule}/price', [SchedulePriceController::class, 'store']);

    Route::put('/price/{price}', [PriceController::class, 'update']);
    Route::delete('/price/{price}', [PriceController::class, 'destroy']);

    Route::post('/schedules/{schedule}/reschedule', [RescheduleRequestController::class, 'store']);
    Route::get('/reschedule-requests', [RescheduleRequestController::class, 'index']);
    Route::post('reschedule-requests/{rescheduleRequest}/accept', [RescheduleRequestController::class, 'accept']);
    Route::post('reschedule-requests/{rescheduleRequest}/decline', [RescheduleRequestController::class, 'decline']);

    /* Payments */
    Route::post('/schedules/{schedule}/promoсode', [PurchaseController::class, 'promocode']);
    Route::post('/schedules/{schedule}/purchase', [PurchaseController::class, 'purchase']);
    /* Payments */


    Route::get('/disciplines/{discipline}/images', [DisciplineController::class, 'indexImage']);
    Route::get('/disciplines/{discipline}/videos', [DisciplineController::class, 'indexVideo']);

    Route::get('/focus-areas/{focusArea}/images', [FocusAreaController::class, 'indexImage']);
    Route::get('/focus-areas/{focusArea}/videos', [FocusAreaController::class, 'indexVideo']);
    Route::get('/focus-areas', [FocusAreaController::class, 'index']);
    Route::get('/focus-areas/{focusArea}', [FocusAreaController::class, 'show']);

    Route::post('messages/users/{user}', [MessageController::class, 'store']);
    Route::get('/messages', [MessageController::class, 'index']);
    Route::get('/messages/receiver/{user}', [MessageController::class, 'showByReceiver']);

    Route::get('/countries', [CountryController::class, 'index']);

    Route::get('timezones', [TimezoneController::class, 'index']);

    Route::get('/users', [UserController::class, 'search']);

    Route::get('/schedule-freezes', [ScheduleFreezesController::class, 'index']);

    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings/{booking}/reschedule', [BookingController::class, 'reschedule']);
    Route::post('/bookings/reschedule', [BookingController::class, 'allReschedule']);

    Route::get('/purchases', [PurchaseController::class, 'index']);
});
