<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ResetPasswordController;
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
Route::post('auth/verify-email', [AuthController::class, 'verifyEmail'])->name('verify-email');

Route::post('auth/forgot-password-ask', [ResetPasswordController::class, 'askForReset']);
Route::post('auth/verify-forgot-password-token', [ResetPasswordController::class, 'verifyToken']);
Route::post('auth/forgot-password-claim', [ResetPasswordController::class, 'claimReset'])
    ->name('claim-reset');


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

    Route::post('articles/{article}/favourite', [ArticleController::class, 'storeFavorite']);
    Route::delete('articles/{article}/favourite', [ArticleController::class, 'deleteFavorite']);
    Route::get('/articles/favourites', [UserController::class, 'articleFavorites']);

    Route::post('practitioners/{practitioner}/favourite', [PractitionerController::class, 'storeFavorite']);
    Route::delete('practitioners/{practitioner}/favourite', [PractitionerController::class, 'deleteFavorite']);
    Route::get('/practitioners/favourites', [UserController::class, 'practitionerFavorites']);

    Route::get('/services', [ServiceController::class, 'index']);
    Route::post('/services', [ServiceController::class, 'store']);
    Route::get('/services/{service}', [ServiceController::class, 'show']);
    Route::put('/services/{service}', [ServiceController::class, 'update']);
    Route::delete('/services/{service}', [ServiceController::class, 'destroy']);

    Route::get('/articles', [ArticleController::class, 'index']);
    Route::post('/articles', [ArticleController::class, 'store']);
    Route::get('/articles/{article}', [ArticleController::class, 'show']);
    Route::put('/articles/{article}', [ArticleController::class, 'edit']);
    Route::delete('/articles/{article}', [ArticleController::class, 'destroy']);

    Route::get('disciplines', [DisciplineController::class, 'index']);
    Route::get('disciplines/list', [DisciplineController::class, 'list']);
    Route::get('disciplines/filter', [DisciplineController::class, 'filter']);
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

    Route::post('/service_types', [ServiceTypeController::class, 'store']);
    Route::get('/service_types', [ServiceTypeController::class, 'index']);
    Route::get('/service_types/list', [ServiceTypeController::class, 'list']);

    Route::post('/services/{service}/schedules', [ScheduleController::class, 'store']);
    Route::get('/services/{service}/schedules', [ScheduleController::class, 'index']);
    Route::post('/schedules/{schedule}/purchase', [ScheduleController::class, 'purchase']);
    Route::get('/schedule/{schedule}/attendants', [ScheduleController::class, 'allUser']);
    Route::post('/schedules/{schedule}/freeze', [ScheduleController::class, 'freeze']);
    Route::get('/schedules/{schedule}/availabilities', [ScheduleController::class, 'availabilities']);

    Route::post('/schedules/{schedule}/reschedule', [RescheduleRequestController::class, 'store']);
    Route::get('/reschedule-requests', [RescheduleRequestController::class, 'index']);
    Route::post('reschedule-requests/{rescheduleRequest}/accept', [RescheduleRequestController::class, 'accept']);
    Route::delete('reschedule-requests/{rescheduleRequest}/decline', [RescheduleRequestController::class, 'decline']);

    Route::post('/schedules/{schedule}/promoсodes', [ScheduleController::class, 'promoCode']);

    Route::get('/disciplines/{discipline}/images', [DisciplineController::class, 'indexImage']);
    Route::get('/disciplines/{discipline}/videos', [DisciplineController::class, 'indexVideo']);

    Route::get('/focus-areas/{focusArea}/images', [FocusAreaController::class, 'indexImage']);
    Route::get('/focus-areas/{focusArea}/videos', [FocusAreaController::class, 'indexVideo']);
    Route::get('/focus-areas', [FocusAreaController::class, 'index']);
    Route::get('/focus-areas/{focusArea}', [FocusAreaController::class, 'show']);

    Route::post('messages/users/{user}', [MessageController::class, 'store']);
    Route::get('/messages', [MessageController::class, 'index']);
    Route::get('/messages/receiver/{user}', [MessageController::class, 'showByReceiver']);

    Route::get('/countries', [CountryController::class, 'filter']);
});
