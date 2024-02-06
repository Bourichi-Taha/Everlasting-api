<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::prefix('categories')->name('categories')->group(function () {
        Route::controller(CategoryController::class)->group(function () {
            Route::get('/', 'readAll');
            Route::post('/', 'createOne');
            Route::get('/{id}', 'readOne');
            Route::put('/{id}', 'updateOne');
            Route::delete('/{id}', 'deleteOne');
        });
    });
    Route::prefix('locations')->name('locations')->group(function () {
        Route::controller(LocationController::class)->group(function () {
            Route::get('/', 'readAll');
            Route::post('/', 'createOne');
            Route::get('/{id}', 'readOne');
            Route::put('/{id}', 'updateOne');
            Route::delete('/{id}', 'deleteOne');
        });
    });
    Route::prefix('events')->name('events')->group(function () {
        Route::controller(EventController::class)->group(function () {
            Route::get('/my-events', 'getUserEvents');
            Route::get('/registered', 'getAllRegistered');
            Route::post('/subscribe', 'registerToEvent');
            Route::post('/unsubscribe', 'unregisterToEvent');
            Route::put('/cancel/{id}', 'cancelEvent');
            Route::post('/', 'createOne');
            Route::put('/{id}', 'updateOne');
            Route::delete('/{id}', 'deleteOne');
            Route::get('/{id}', 'readOne');
            Route::get('/', 'readAll');
        });
    });
});
