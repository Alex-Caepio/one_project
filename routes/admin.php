<?php

use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\ClientController;
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
        Route::delete('/practitioners/{practitioner}', [PractitionerController::class, 'destroy']);
        Route::post('/practitioners/{practitioner}/unpublish', [PractitionerController::class, 'unpublish']);
        Route::post('/practitioners/{practitioner}/publish', [PractitionerController::class, 'publish']);

        Route::get('/clients', [ClientController::class, 'index']);
        Route::post('/clients', [ClientController::class, 'store']);
        Route::get('/clients/{client}', [ClientController::class, 'show']);
        Route::put('/clients/{client}', [ClientController::class, 'update']);
        Route::delete('/clients/{client}', [ClientController::class, 'destroy']);

        Route::get('/services', [ServiceController::class, 'index']);
        Route::post('/services', [ServiceController::class, 'store']);
        Route::get('/services/{service}', [ServiceController::class, 'show']);
        Route::put('/services/{service}', [ServiceController::class, 'update']);
        Route::delete('/services/{service}', [ServiceController::class, 'destroy']);

        Route::get('/plans', [PlanController::class, 'index']);
        Route::post('/plans', [PlanController::class, 'store']);
        Route::get('/plans/{plan}', [PlanController::class, 'show']);
        Route::put('/plans/{plan}', [PlanController::class, 'update']);
        Route::delete('/plans/{plan}', [PlanController::class, 'destroy']);

        Route::put('/profile',[AdminProfileController::class, 'update']);

        Route::get('/profile',[ProfileController::class, 'show']);
        Route::put('/profile',[ProfileController::class, 'update']);

        Route::get('/focus-areas', [FocusAreaController::class, 'index']);
        Route::put('/focus-areas/{focusArea}/update', [FocusAreaController::class, 'update']);
        Route::post('/focus-areas', [FocusAreaController::class, 'store']);
        Route::get('/focus-areas/{focusArea}', [FocusAreaController::class, 'show']);
        Route::delete('/focus-areas/{focusArea}/destroy', [FocusAreaController::class, 'destroy']);
        Route::post('/focus-areas/{focusArea}/image', [FocusAreaController::class, 'image']);
        Route::post('/focus-areas/{focusArea}/images', [FocusAreaController::class, 'storeImages']);
        Route::post('/focus-areas/{focusArea}/videos',[FocusAreaController::class, 'storeVideos']);
        Route::post('/focus-areas/{focusArea}/icon', [FocusAreaController::class, 'icon']);

        Route::get('/promotion-codes', [PromotionCodeController::class, 'index']);
        Route::put('/promotion-codes/{promotionCode}/update', [PromotionCodeController::class, 'update']);
        Route::post('/promotion-codes', [PromotionCodeController::class, 'store']);
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
        Route::get('/transactional-emails/{customEmail}', [CustomEmailController::class, 'show']);
        Route::put('/transactional-emails/{customEmail}', [CustomEmailController::class, 'update']);
        Route::delete('/transactional-emails/{customEmail}', [CustomEmailController::class, 'destroy']);

        Route::get('/articles', [ArticleController::class, 'index']);
        Route::delete('/articles/{article}', [ArticleController::class, 'destroy']);

        Route::post('/articles/{article}/publish', [ArticleController::class, 'publish']);
        Route::post('/articles/{article}/unpublish', [ArticleController::class, 'unpublish']);
    });
});
