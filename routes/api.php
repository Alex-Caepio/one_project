<?php

use Illuminate\Support\Facades\Route;

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

Route::post('auth/register', 'Auth\AuthController@register');
Route::post('auth/login', 'Auth\AuthController@login');
Route::get('auth/verify-email', 'Auth\AuthController@verifyEmail')->name('verify-email');

Route::post('auth/forgot-password-ask', 'Auth\ResetPasswordController@askForReset');
Route::post('auth/forgot-password-claim', 'Auth\ResetPasswordController@claimReset');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('auth/profile', 'Auth\AuthController@profile');
    Route::put('auth/profile', 'Auth\AuthController@update');
    Route::post('auth/resend-verification', 'Auth\AuthController@resendVerification');
    Route::post('auth/profile/avatar', 'Auth\AuthController@avatar');
    Route::post('auth/profile/background', 'Auth\AuthController@background');

    Route::post('services/{service}/favourite', 'ServiceController@storeFavorite');
    Route::delete('services/{service}/favourite', 'ServiceController@deleteFavorite');
    Route::get('/services/favourites', 'UserController@serviceFavorites');

    Route::post('articles/{article}/favourite', 'ArticleController@storeFavorite');
    Route::delete('articles/{article}/favourite', 'ArticleController@deleteFavorite');
    Route::get('/articles/favourites', 'UserController@ArticleFavorites');

    Route::post('practitioners/{practitioner}/favourite', 'PractitionerController@storeFavorite');
    Route::delete('practitioners/{practitioner}/favourite', 'PractitionerController@deleteFavorite');
    Route::get('/practitioners/favourites', 'UserController@PractitionerFavorites');

    Route::resource('services', 'ServiceController');
    Route::resource('articles', 'ArticleController');

    Route::get('disciplines', 'DisciplineController@index');
    Route::get('disciplines/list', 'DisciplineController@list');
    Route::get('disciplines/filter', 'DisciplineController@filter');

    Route::get('keywords', 'KeywordController@index');
    Route::get('keywords/list', 'KeywordController@list');
    Route::get('keywords/filter', 'KeywordController@filter');

    Route::get('locations', 'LocationController@index');
    Route::get('locations/list', 'LocationController@list');

    Route::get('practitioners', 'PractitionerController@index');
    Route::get('practitioners/list', 'PractitionerController@list');

    Route::post('/credit-card', 'CardStripeController@store');
    Route::get('/credit-cards', 'CardStripeController@index');


    Route::get('/plans', 'PlanController@index');
    Route::post('/plans/{plan}/purchase', 'PlanController@purchase');

    Route::post('/service_types', 'ServiceTypeController@store');
    Route::get('/service_types', 'ServiceTypeController@index');
    Route::get('/service_types/list', 'ServiceTypeController@list');

    Route::post('/services/{service}/schedule', 'ScheduleController@store');
    Route::get('/services/{service}/schedule', 'ScheduleController@index');
    Route::post('/schedules/{schedule}/purchase', 'ScheduleController@purchase');
    Route::get('/schedule/{schedule}/attendants', 'ScheduleController@allUser');
    Route::post('/schedules/{schedule}/freeze','ScheduleController@freeze');
    Route::get('/schedules/{schedule}/availabilities','ScheduleController@availabilities');

    Route::post('/schedule/{schedule}/reschedule', 'RescheduleRequestController@store');
    Route::get('/reschedule-requests', 'RescheduleRequestController@index');
    Route::post('reschedule-requests/{rescheduleRequest}/accept', 'RescheduleRequestController@accept');
    Route::delete('reschedule-requests/{rescheduleRequest}/decline', 'RescheduleRequestController@decline');

    Route::post('/schedule/{schedule}/promoсode','ScheduleController@promoCode');

    Route::post('message/users/{user}','UserController@sendMail');

    Route::get('/disciplines/{discipline}/images','DisciplineController@indexImage');
    Route::get('/disciplines/{discipline}/videos','DisciplineController@indexVideo');

    Route::get('/focus-area/{focusArea}/images','FocusAreaController@indexImage');
    Route::get('/focus-area/{focusArea}/videos','FocusAreaController@indexVideo');
});


