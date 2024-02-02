<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
});

Route::prefix('categories')->name('categories')->group(function () {
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/', 'readAll');
    });
    Route::controller(CategoryController::class)->group(function () {
        Route::post('/', 'createOne');
    });
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/{id}', 'readOne');
    });
    Route::controller(CategoryController::class)->group(function () {
        Route::put('/{id}', 'updateOne');
    });
    Route::controller(CategoryController::class)->group(function () {
        Route::delete('/{id}', 'deleteOne');
    });
});
Route::prefix('locations')->name('locations')->group(function () {
    Route::controller(LocationController::class)->group(function () {
        Route::get('/', 'readAll');
    });
    Route::controller(LocationController::class)->group(function () {
        Route::post('/', 'createOne');
    });
    Route::controller(LocationController::class)->group(function () {
        Route::get('/{id}', 'readOne');
    });
    Route::controller(LocationController::class)->group(function () {
        Route::put('/{id}', 'updateOne');
    });
    Route::controller(LocationController::class)->group(function () {
        Route::delete('/{id}', 'deleteOne');
    });
});
Route::prefix('events')->name('events')->group(function () {
    Route::controller(EventController::class)->group(function () {
        Route::get('/myevents', 'getUserEvents');
    });
    Route::controller(EventController::class)->group(function () {
        Route::get('/registered', 'getAllRegistered');
    });
    Route::controller(EventController::class)->group(function () {
        Route::post('/subscribe', 'registerToEvent');
    });
    Route::controller(EventController::class)->group(function () {
        Route::post('/unsubscribe', 'unregisterToEvent');
    });
    Route::controller(EventController::class)->group(function () {
        Route::put('/cancel/{id}', 'cancelEvent');
    });
    Route::controller(EventController::class)->group(function () {
        Route::post('/', 'createOne');
    });
    Route::controller(EventController::class)->group(function () {
        Route::put('/{id}', 'updateOne');
    });
    Route::controller(EventController::class)->group(function () {
        Route::delete('/{id}', 'deleteOne');
    });
    Route::controller(EventController::class)->group(function () {
        Route::get('/{id}', 'readOne');
    });
    Route::controller(EventController::class)->group(function () {
        Route::get('/', 'readAll');
    });
});
