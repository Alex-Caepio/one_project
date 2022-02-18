<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\QuoteController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingMyClientController;
use App\Http\Controllers\CancellationController;
use App\Http\Controllers\GoogleCalendarIntegrationController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\LatestTwoController;
use App\Http\Controllers\MainPageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ScheduleFreezesController;
use App\Http\Controllers\SchedulePriceController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StripeAccountController;
use App\Http\Controllers\StripeWebhookController;
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
use App\Http\Controllers\ScheduleBookingController;
use App\Http\Controllers\RescheduleRequestController;
use App\Http\Controllers\FocusAreaController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\TimezoneController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\EmailLinkHandlerController;

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
Route::post('auth/resend-verification', [AuthController::class, 'resendVerification']);

Route::middleware(['stripe'])->group(function () {
    Route::match(['post', 'get'], '/stripe-webhook', [StripeWebhookController::class, 'handler']);
});

/* Public Routes For Services And Articles */
Route::get('articles', [ArticleController::class, 'index']);
Route::get('articles/{publicArticle}', [ArticleController::class, 'show']);
Route::post('articles/{article}/copy', [ArticleController::class, 'copy']);

Route::get('services', [ServiceController::class, 'index']);
Route::get('services/{publicService}', [ServiceController::class, 'show']);

Route::get('/mainpage', [MainPageController::class, 'index']);

Route::get('/plans', [PlanController::class, 'index']);

Route::get('/service-types', [ServiceTypeController::class, 'index']);

Route::get('disciplines', [DisciplineController::class, 'index']);
Route::get('disciplines/{discipline}', [DisciplineController::class, 'show']);

Route::get('/focus-areas', [FocusAreaController::class, 'index']);
Route::get('/focus-areas/{focusArea}', [FocusAreaController::class, 'show']);


Route::get('practitioners', [PractitionerController::class, 'index']);
Route::get('users/{slug}', [AuthController::class, 'show']);

Route::get('/services/{service}/schedules', [ScheduleController::class, 'index']);

Route::get('/search', [SearchController::class, 'index']);
Route::get('/latest-two', [LatestTwoController::class, 'index']);
Route::get('/practitioners/{user}', [UserController::class, 'show']);

Route::middleware(['auth:reschedule-token'])->group(function () {
    Route::match(
        ['post', 'get'],
        '/accept_reschedule/{rescheduleRequest}',
        [EmailLinkHandlerController::class, 'acceptReschedule']
    );
    Route::match(
        ['post', 'get'],
        '/decline_reschedule/{rescheduleRequest}',
        [EmailLinkHandlerController::class, 'declineReschedule']
    );
});

Route::get('/schedules/{schedule}/available-instalments', [ScheduleController::class, 'availableInstalments']);
Route::get('/prices/{price}/appointments-dates/{date}', [ScheduleController::class, 'appointmentsOnDate']);


Route::middleware(['auth:sanctum', 'unsuspended'])->group(function () {
    Route::post('/gcal/auth', [GoogleCalendarIntegrationController::class, 'auth'])->name('gcal-auth');
    Route::get('/gcal/events', [GoogleCalendarIntegrationController::class, 'getEventList']);
    Route::get('/gcal/settings', [GoogleCalendarIntegrationController::class, 'getSettings']);
    Route::post('/gcal/settings', [GoogleCalendarIntegrationController::class, 'updateSettings']);

    Route::get('auth/profile', [AuthController::class, 'profile']);
    Route::put('auth/profile', [AuthController::class, 'update']);
    Route::delete('auth/profile', [AuthController::class, 'delete']);
    Route::post('auth/profile/avatar', [AuthController::class, 'avatar']);
    Route::post('auth/profile/background', [AuthController::class, 'background']);
    Route::get('auth/quotes/articles', [QuoteController::class, 'quotesArticles']);
    Route::get('auth/quotes/services/{service}/schedules', [QuoteController::class, 'quotesServices']);
    Route::get('auth/quotes/schedules/{schedule}/prices', [QuoteController::class, 'quotesPrices']);

    Route::get('stripe/link', [StripeAccountController::class, 'link']);
    Route::get('stripe/account', [StripeAccountController::class, 'account']);

    Route::post('services/{service}/favourite', [ServiceController::class, 'storeFavorite']);
    Route::delete('services/{service}/favourite', [ServiceController::class, 'deleteFavorite']);
    Route::get('/services/favourites', [UserController::class, 'serviceFavorites']);

    Route::post('practitioners/{practitioner}/favourite', [PractitionerController::class, 'storeFavorite']);
    Route::delete('practitioners/{practitioner}/favourite', [PractitionerController::class, 'deleteFavorite']);
    Route::get('/practitioners/favourites', [UserController::class, 'practitionerFavorites']);

    Route::middleware(['practitioner'])->group(function () {
        /* Profile Update */
        Route::put('/auth/business-profile', [AuthController::class, 'updateBusiness']);
        Route::put('/auth/business-media', [AuthController::class, 'updateMedia']);
        Route::post('/auth/publish', [AuthController::class, 'publish']);
        Route::post('/auth/unpublish', [AuthController::class, 'unpublish']);
        Route::post('/stripe-connected', [StripeAccountController::class, 'stripeConnected']);

        Route::get('articles-practitioner', [ArticleController::class, 'practitionerArticleList']);
        Route::get('articles-practitioner/{article}', [ArticleController::class, 'practitionerArticleShow']);
        Route::post('articles', [ArticleController::class, 'store']);
        Route::put('articles/{article}', [ArticleController::class, 'update']);
        Route::delete('articles/{article}', [ArticleController::class, 'destroy']);

        Route::get('services-practitioner', [ServiceController::class, 'practitionerServiceList']);
        Route::get('purchase-practitioner/{booking}', [PurchaseController::class, 'show']);
        Route::get('services-practitioner/{service}', [ServiceController::class, 'practitionerServiceShow']);
        Route::post('services', [ServiceController::class, 'store']);
        Route::put('services/{service}', [ServiceController::class, 'update']);
        Route::post('services/{service}/publish', [ServiceController::class, 'publish']);
        Route::post('services/{service}/unpublish', [ServiceController::class, 'unpublish']);
        Route::delete('services/{service}', [ServiceController::class, 'destroy']);
        Route::post('/services/{service}/copy', [ServiceController::class, 'copy']);

        Route::get('/services/{service}/practitioner-schedules', [ScheduleController::class, 'ownerScheduleList']);
        Route::post('/services/{service}/schedules', [ScheduleController::class, 'store']);
    });

    Route::post('/articles/{article}/favourite', [ArticleController::class, 'storeFavorite']);
    Route::delete('/articles/{article}/favourite', [ArticleController::class, 'deleteFavorite']);
    Route::get('/articles/favourites', [UserController::class, 'articleFavorites']);
    Route::post('/articles/{article}/publish', [ArticleController::class, 'publish']);
    Route::post('/articles/{article}/unpublish', [ArticleController::class, 'unpublish']);

    Route::get('keywords', [KeywordController::class, 'index']);
    Route::get('keywords/list', [KeywordController::class, 'list']);
    Route::get('keywords/filter', [KeywordController::class, 'filter']);

    Route::get('locations', [LocationController::class, 'index']);
    Route::get('locations/list', [LocationController::class, 'list']);

    Route::get('practitioners/list', [PractitionerController::class, 'list']);

    Route::post('/credit-cards', [CardStripeController::class, 'store']);
    Route::get('/credit-cards', [CardStripeController::class, 'index']);

    Route::post('/plans/{plan}/purchase', [PlanController::class, 'purchase']);
    Route::post('/plans/{plan}/finalize', [PlanController::class, 'finalize']);
    Route::post('/plans/{plan}/purchase-trial', [PlanController::class, 'purchaseFree']);


    Route::get('/schedules/{schedule}', [ScheduleController::class, 'show']);
    Route::put('/schedules/{schedule}', [ScheduleController::class, 'update']);
    Route::get('/schedules/{schedule}/attendants', [ScheduleController::class, 'allUser']);
    Route::get('/schedules/{schedule}/bookings', [ScheduleController::class, 'allBookings']);
    Route::post('/schedules/{schedule}/freeze', [ScheduleController::class, 'freeze']);
    Route::get('/schedules/{schedule}/availabilities', [ScheduleController::class, 'availabilities']);
    Route::post('/schedules/{schedule}/price', [SchedulePriceController::class, 'store']);
    Route::post('/schedules/{schedule}/publish', [ScheduleController::class, 'publish']);
    Route::post('/schedules/{schedule}/unpublish', [ScheduleController::class, 'unpublish']);
    Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy']);
    Route::post('/schedules/{schedule}/copy', [ScheduleController::class, 'copy']);
    Route::post('/schedules/{schedule}/calendar-instalments', [ScheduleController::class, 'availableInstalmentsDates']);
    Route::get('/schedules/{schedule}/reschedule-available', [ScheduleController::class, 'rescheduleScheduleList']);

    Route::put('/price/{price}', [PriceController::class, 'update']);
    Route::delete('/price/{price}', [PriceController::class, 'destroy']);

    Route::get('/reschedule-requests', [RescheduleRequestController::class, 'index']);
    Route::get('/reschedule-requests/inbound', [RescheduleRequestController::class, 'inbound']);
    Route::get('/reschedule-requests/outbound', [RescheduleRequestController::class, 'outbound']);
    Route::post('reschedule-requests/{rescheduleRequest}/accept', [RescheduleRequestController::class, 'accept']);
    Route::post('reschedule-requests/{rescheduleRequest}/decline', [RescheduleRequestController::class, 'decline']);

    Route::post('/bookings/{booking}/reschedule', [RescheduleRequestController::class, 'reschedule']);
    Route::post('/bookings/reschedule', [RescheduleRequestController::class, 'allReschedule']);
    Route::post('/bookings/schedule/{schedule}/reschedule', [RescheduleRequestController::class, 'scheduleReschedule']);


    /* Payments */
    Route::post('/schedules/{schedule}/promocode', [PurchaseController::class, 'validatePromocode']);
    Route::post('/schedules/{schedule}/purchase', [PurchaseController::class, 'purchase'])->name('purchase-process');
    Route::patch('/purchases/{purchase}/finalize', [PurchaseController::class, 'finalize']);
    /* Payments */

    Route::get('/schedules/{schedule}/upcoming-bookings', [ScheduleBookingController::class, 'index']);

    Route::get('/disciplines/{discipline}/images', [DisciplineController::class, 'indexImage']);
    Route::get('/disciplines/{discipline}/videos', [DisciplineController::class, 'indexVideo']);

    Route::post('messages/users/{user}', [MessageController::class, 'store']);
    Route::post('messages/users', [MessageController::class, 'storeMultiple']);
    Route::get('/messages', [MessageController::class, 'index']);
    Route::get('/messages/receiver/{user}', [MessageController::class, 'showByReceiver']);
    Route::get('/messages/conversation/{conversationId}', [MessageController::class, 'getConversation']);

    Route::get('/countries', [CountryController::class, 'index']);

    Route::get('timezones', [TimezoneController::class, 'index']);

    Route::get('/users', [UserController::class, 'search']);

    Route::get('/schedule-freezes', [ScheduleFreezesController::class, 'index']);
    Route::delete('/schedule-freezes/{scheduleFreeze}', [ScheduleFreezesController::class, 'destroy']);

    Route::get('/bookings/my-clients', [BookingMyClientController::class, 'index']);
    Route::get('/bookings/my-clients-purchases', [BookingMyClientController::class, 'purchases']);
    Route::get('/bookings/my-clients-upcoming', [BookingMyClientController::class, 'upcoming']);
    Route::get('/bookings/my-clients-closed', [BookingMyClientController::class, 'closed']);

    Route::get('/bookings', [BookingController::class, 'index']);
    Route::get('/bookings/{booking}', [BookingController::class, 'show']);
    Route::post('/bookings/{booking}/complete', [BookingController::class, 'complete']);

    Route::get('/purchases', [PurchaseController::class, 'index']);

    Route::get('/payment-methods', [PaymentMethodController::class, 'index']);
    Route::post('/payment-methods', [PaymentMethodController::class, 'attach']);
    Route::post('/payment-methods/default', [PaymentMethodController::class, 'default']);
    Route::post('/payment-methods/default-fee', [PaymentMethodController::class, 'defaultFee']);
    Route::put('/payment-methods', [PaymentMethodController::class, 'update']);
    Route::delete('/payment-methods', [PaymentMethodController::class, 'detach']);

    /* Cancellation */
    Route::get('/cancellations', [CancellationController::class, 'index']);
    Route::post('/cancellations/bookings', [CancellationController::class, 'cancelManyBookings']);
    Route::post('/cancellations/schedule/{schedule}', [CancellationController::class, 'cancelSchedule']);
    Route::post('/cancellations/{booking}', [CancellationController::class, 'cancelBooking']);

    Route::post('/images', [ImageController::class, 'upload']);

    Route::get('/notifications/practitioner', [NotificationController::class, 'index']);
    Route::get('/notifications/client', [NotificationController::class, 'clientNotifications']);
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead']);

    Route::get('/installments/{purchase}', [InstallmentController::class, 'getInstallments']);
});
