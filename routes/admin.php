<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CustomEmailController;
use App\Http\Controllers\Admin\DisciplineController;
use App\Http\Controllers\Admin\FocusAreaController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PromotionCodeController;
use App\Http\Controllers\Admin\PractitionerController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\PlanController;

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

        Route::get('/admins', [AdminController::class, 'index']);
        Route::post('/admins', [AdminController::class, 'store']);
        Route::get('/admins/{admin}', [AdminController::class, 'show']);
        Route::put('/profile',[AdminController::class, 'update']);
        Route::delete('/admins/{admin}', [AdminController::class, 'destroy']);

        Route::get('/profile',[AdminController::class, 'indexProfile']);

        Route::get('/focus-areas', [FocusAreaController::class, 'index']);
        Route::put('/focus-areas/{focusArea}/update', [FocusAreaController::class, 'update']);
        Route::post('/focus-areas', [FocusAreaController::class, 'store']);
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
        Route::post('/disciplines/{discipline}/images',[DisciplineController::class, 'storeImage']);
        Route::post('/disciplines/{discipline}/videos',[DisciplineController::class, 'storeVideo']);
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

    });
});
