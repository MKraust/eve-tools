<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers;

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

Route::prefix('settings')->group(function () {
    Route::get('/market-orders-update-info', [Controllers\Api\SettingsController::class, 'getMarketOrdersUpdateInfo']);
    Route::post('/refresh-market-orders',     [Controllers\Api\SettingsController::class, 'refreshMarketOrders']);
});

Route::prefix('production')->group(function () {
    Route::get('/modules/search',    [Controllers\Api\ProductionController::class, 'searchModules']);

    Route::prefix('favorites')->group(function () {
        Route::get('/list',    [Controllers\Api\ProductionController::class, 'getFavorites']);
        Route::post('/add',    [Controllers\Api\ProductionController::class, 'addFavorite']);
        Route::post('/delete', [Controllers\Api\ProductionController::class, 'deleteFavorite']);
    });

    Route::prefix('tracked')->group(function () {
        Route::get('/list',                [Controllers\Api\ProductionController::class, 'getTrackedTypes']);
        Route::post('/add',                [Controllers\Api\ProductionController::class, 'addTrackedType']);
        Route::post('/edit',               [Controllers\Api\ProductionController::class, 'editTrackedType']);
        Route::post('/delete',             [Controllers\Api\ProductionController::class, 'deleteTrackedType']);
        Route::post('/update-done-counts', [Controllers\Api\ProductionController::class, 'updateTrackedTypeDoneCounts']);
        Route::get('/shopping-list',       [Controllers\Api\ProductionController::class, 'getShoppingList']);
    });
});

Route::prefix('trading')->group(function () {
    Route::get('/modules/search',    [Controllers\Api\TradingController::class, 'searchModules']);

    Route::prefix('favorites')->group(function () {
        Route::get('/list',    [Controllers\Api\TradingController::class, 'getFavorites']);
        Route::post('/add',    [Controllers\Api\TradingController::class, 'addFavorite']);
        Route::post('/delete', [Controllers\Api\TradingController::class, 'deleteFavorite']);
    });
});
