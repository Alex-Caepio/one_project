<?php

use App\Http\Controllers\Admin\PractitionerCommissionController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\MainPageController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\PractitionerSubscriptionCommissionController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\FocusAreaController;
use App\Http\Controllers\Admin\DisciplineController;
use App\Http\Controllers\Admin\CustomEmailController;
use App\Http\Controllers\Admin\PractitionerController;
use App\Http\Controllers\Admin\PromotionCodeController;
use App\Http\Controllers\Admin\ArticleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::middleware(['admin'])->group(function () {
        Route::get('/practitioners', [PractitionerController::class, 'index']);
        Route::post('/practitioners', [PractitionerController::class, 'store']);
        Route::get('/practitioners/{practitioner}', [PractitionerController::class, 'show']);
        Route::put('/practitioners/{practitioner}', [PractitionerController::class, 'update']);
        //delete has post method on purpose and doesn't require refactoring
        Route::post('/practitioners/{practitioner}/delete', [PractitionerController::class, 'destroy']);
        Route::post('/practitioners/{practitioner}/unpublish', [PractitionerController::class, 'unpublish']);
        Route::post('/practitioners/{practitioner}/publish', [PractitionerController::class, 'publish']);

        Route::get('/clients', [ClientController::class, 'index']);
        Route::post('/clients', [ClientController::class, 'store']);
        Route::get('/clients/{client}', [ClientController::class, 'show']);
        Route::put('/clients/{client}', [ClientController::class, 'update']);
        //delete has post method on purpose and doesn't require refactoring
        Route::post('/clients/{client}/delete', [ClientController::class, 'destroy']);

        Route::get('/services', [ServiceController::class, 'index']);
        Route::post('/services', [ServiceController::class, 'store']);
        Route::get('/services/{service}', [ServiceController::class, 'show']);
        Route::put('/services/{service}', [ServiceController::class, 'update']);
        Route::delete('/services/{service}', [ServiceController::class, 'destroy']);
        Route::post('services/{service}/publish', [ServiceController::class, 'publish']);
        Route::post('services/{service}/unpublish', [ServiceController::class, 'unpublish']);

        Route::get('/plans', [PlanController::class, 'index']);
        Route::post('/plans', [PlanController::class, 'store']);
        Route::get('/plans/{plan}', [PlanController::class, 'show']);
        Route::put('/plans/{firstPlan}/swap-order/{secondPlan}', [PlanController::class, 'swapOrder']);
        Route::put('/plans/{plan}', [PlanController::class, 'update']);
        Route::delete('/plans/{plan}', [PlanController::class, 'destroy']);

        Route::get('/profile',[ProfileController::class, 'show']);
        Route::put('/profile',[ProfileController::class, 'update']);

        Route::get('/focus-areas', [FocusAreaController::class, 'index']);
        Route::put('/focus-areas/{focusArea}', [FocusAreaController::class, 'update']);
        Route::post('/focus-areas', [FocusAreaController::class, 'store']);
        Route::get('/focus-areas/{focusArea}', [FocusAreaController::class, 'show']);
        Route::delete('/focus-areas/{focusArea}', [FocusAreaController::class, 'destroy']);
        Route::post('/focus-areas/{focusArea}/unpublish', [FocusAreaController::class, 'unpublish']);
        Route::post('/focus-areas/{focusArea}/publish', [FocusAreaController::class, 'publish']);
        //probably below are deprecated
        Route::post('/focus-areas/{focusArea}/image', [FocusAreaController::class, 'image']);
        Route::post('/focus-areas/{focusArea}/images', [FocusAreaController::class, 'storeImages']);
        Route::post('/focus-areas/{focusArea}/videos',[FocusAreaController::class, 'storeVideos']);
        Route::post('/focus-areas/{focusArea}/icon', [FocusAreaController::class, 'icon']);

        Route::get('/promotions', [PromotionController::class, 'index']);
        Route::post('/promotions', [PromotionController::class, 'store']);
        Route::get('/promotions/{promotionWithTrashed}', [PromotionController::class, 'show']);
        Route::post('/promotions/{promotion}/enable', [PromotionController::class, 'enable']);
        Route::post('/promotions/{promotion}/disable', [PromotionController::class, 'disable']);
        Route::delete('/promotions/{promotion}', [PromotionController::class, 'destroy']);
        Route::put('/promotions/{promotion}', [PromotionController::class, 'update']);

        Route::get('/promotion-codes', [PromotionCodeController::class, 'index']);
        Route::get('/promotion-codes/export', [PromotionCodeController::class, 'export']);
        Route::post('/promotion-codes', [PromotionCodeController::class, 'store']);
        Route::put('/promotion-codes/{promotionCode}/update', [PromotionCodeController::class, 'update']);
        Route::post('/promotion-codes/{promotionCode}/enable', [PromotionCodeController::class, 'enable']);
        Route::post('/promotion-codes/{promotionCode}/disable', [PromotionCodeController::class, 'disable']);
        Route::delete('/promotion-codes/{promotionCode}/destroy', [PromotionCodeController::class, 'destroy']);

        Route::get('/disciplines', [DisciplineController::class, 'index']);
        Route::post('/disciplines', [DisciplineController::class, 'store']);
        Route::get('/disciplines/{discipline}', [DisciplineController::class, 'show']);
        Route::put('/disciplines/{discipline}', [DisciplineController::class, 'update']);
        Route::delete('/disciplines/{discipline}', [DisciplineController::class, 'destroy']);
        Route::post('/disciplines/{discipline}/unpublish', [DisciplineController::class, 'unpublish']);
        Route::post('/disciplines/{discipline}/publish', [DisciplineController::class, 'publish']);

        Route::get('/transactional-emails', [CustomEmailController::class, 'index']);
        Route::post('/transactional-emails', [CustomEmailController::class, 'store']);
        Route::post('/transactional-emails/footer', [CustomEmailController::class, 'storeFooter']);
        Route::get('/transactional-emails/footer', [CustomEmailController::class, 'getFooter']);
        Route::get('/transactional-emails/{customEmail}', [CustomEmailController::class, 'show']);
        Route::put('/transactional-emails/{customEmail}', [CustomEmailController::class, 'update']);
        Route::delete('/transactional-emails/{customEmail}', [CustomEmailController::class, 'destroy']);

        Route::get('/articles', [ArticleController::class, 'index']);
        Route::get('/articles/{article}', [ArticleController::class, 'show']);
        Route::put('/articles/{article}', [ArticleController::class, 'update']);
        Route::delete('/articles/{article}', [ArticleController::class, 'destroy']);

        Route::post('/articles/{article}/publish', [ArticleController::class, 'publish']);
        Route::post('/articles/{article}/unpublish', [ArticleController::class, 'unpublish']);

        Route::get('/mainpage', [MainPageController::class, 'index']);
        Route::put('/mainpage', [MainPageController::class, 'update']);

        Route::get('/bookings', [BookingController::class, 'index']);

        Route::get('/practitioner-commissions',[PractitionerCommissionController::class,'index']);
        Route::get('/practitioner-commissions/{practitionerCommission}',[PractitionerCommissionController::class,'show']);
        Route::post('/practitioner-commissions',[PractitionerCommissionController::class,'store']);
        Route::put('/practitioner-commissions/{practitionerCommission}',[PractitionerCommissionController::class,'update']);
        Route::delete('/practitioner-commissions/{practitionerCommission}',[PractitionerCommissionController::class,'delete']);

        Route::get('/practitioner-subscription-commissions',[PractitionerSubscriptionCommissionController::class,'index']);
        Route::get('/practitioner-subscription-commissions/{subscriptionCommission}',[PractitionerSubscriptionCommissionController::class,'show']);
        Route::post('/practitioner-subscription-commissions',[PractitionerSubscriptionCommissionController::class,'store']);
        Route::put('/practitioner-subscription-commissions/{subscriptionCommission}',[PractitionerSubscriptionCommissionController::class,'update']);
        Route::delete('/practitioner-subscription-commissions/{subscriptionCommission}',[PractitionerSubscriptionCommissionController::class,'delete']);


        Route::get('/schedules', [ScheduleController::class, 'index']);
        Route::post('/schedules/service/{service}', [ScheduleController::class, 'store']);
        Route::get('/schedules/{schedule}', [ScheduleController::class, 'show']);
        Route::put('/schedules/{schedule}', [ScheduleController::class, 'update']);
        Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy']);
        Route::post('/schedules/{schedule}/publish', [ScheduleController::class, 'publish']);
        Route::post('/schedules/{schedule}/unpublish', [ScheduleController::class, 'unpublish']);


    });
});
