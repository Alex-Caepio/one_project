<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::middleware(['admin'])->group(function () {
        Route::group(['namespace' => 'Admin'], function () {
            Route::resource('practitioner', 'PractitionerController');
            Route::resource('client', 'ClientController');
            Route::resource('service', 'ServiceController');
            Route::resource('plan', 'PlanController');
            Route::resource('admin', 'AdminController');
        });
        Route::get('/profile','Admin\AdminController@indexProfile');
        Route::put('/profile','Admin\AdminController@updateProfile');
        Route::get('/focus-area', 'Admin\FocusAreaController@index');
        Route::put('/focus-area/{focusArea}/update', 'Admin\FocusAreaController@update');
        Route::post('/focus-area', 'Admin\FocusAreaController@store');
        Route::delete('/focus-area/{focusArea}/destroy', 'Admin\FocusAreaController@destroy');
        Route::post('/focus-area/{focusArea}/image', 'Admin\FocusAreaController@image');
        Route::post('/focus-area/{focusArea}/images', 'Admin\FocusAreaController@storeImages');
        Route::post('/focus-area/{focusArea}/videos','Admin\FocusAreaController@storeVideo');
        Route::post('/focus-area/{focusArea}/icon', 'Admin\FocusAreaController@icon');

        Route::get('/promotion-code', 'Admin\PromotionCodeController@index');
        Route::put('/promotion-code/{promotionCode}/update', 'Admin\PromotionCodeController@update');
        Route::post('/promotion-code', 'Admin\PromotionCodeController@store');
        Route::delete('/promotion-code/{promotionCode}/destroy', 'Admin\PromotionCodeController@destroy');

        Route::get('/discipline', 'Admin\DisciplineController@index');
        Route::post('/discipline', 'Admin\DisciplineController@store');
        Route::post('/discipline/{discipline}/images','Admin\DisciplineController@storeImage');
        Route::post('/discipline/{discipline}/videos','Admin\DisciplineController@storeVideo');
        Route::get('/discipline/{discipline}', 'Admin\DisciplineController@show');
        Route::put('/discipline/{discipline}', 'Admin\DisciplineController@update');
        Route::delete('/discipline/{discipline}', 'Admin\DisciplineController@destroy');

        Route::get('/transactional-emails', 'Admin\CustomEmailController@index');
        Route::post('/transactional-emails', 'Admin\CustomEmailController@store');
        Route::get('/transactional-emails/{customEmail}', 'Admin\CustomEmailController@show');
        Route::put('/transactional-emails/{customEmail}', 'Admin\CustomEmailController@update');
        Route::delete('/transactional-emails/{customEmail}', 'Admin\CustomEmailController@destroy');

    });
});
