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
    Route::get('/market-orders-update-info',     [Controllers\Api\SettingsController::class, 'getMarketOrdersUpdateInfo']);
    Route::get('/market-history-update-info',    [Controllers\Api\SettingsController::class, 'getMarketHistoryUpdateInfo']);
    Route::post('/refresh-market-data',          [Controllers\Api\SettingsController::class, 'refreshMarketData']);
    Route::post('/refresh-market-history',       [Controllers\Api\SettingsController::class, 'refreshMarketHistory']);
});

Route::prefix('characters')->group(function () {
    Route::post('/role/toggle', [Controllers\Api\CharactersController::class, 'toggleRole']);
});

Route::prefix('production')->group(function () {
    Route::get('/modules/search',    [Controllers\Api\ProductionController::class, 'searchModules']);
    Route::get('/profitable/list',   [Controllers\Api\ProductionController::class, 'getProfitableItems']);

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
    Route::get('/modules/search',       [Controllers\Api\TradingController::class, 'searchModules']);
    Route::get('/profitable/list',      [Controllers\Api\TradingController::class, 'getProfitableItems']);
    Route::post('/open-market-details', [Controllers\Api\TradingController::class, 'openMarketDetails']);
    Route::get('/stats-by-half-hour',   [Controllers\Api\TradingController::class, 'getMoneyFlowStatistics']);
    Route::get('/unlisted',             [Controllers\Api\TradingController::class, 'getUnlistedItems']);

    Route::prefix('favorites')->group(function () {
        Route::get('/list',    [Controllers\Api\TradingController::class, 'getFavorites']);
        Route::post('/add',    [Controllers\Api\TradingController::class, 'addFavorite']);
        Route::post('/delete', [Controllers\Api\TradingController::class, 'deleteFavorite']);
    });

    Route::prefix('orders')->group(function () {
        Route::get('/list', [Controllers\Api\TradingController::class, 'getOrders']);
    });

    Route::prefix('delivery')->group(function () {
        Route::get('/list',    [Controllers\Api\TradingController::class, 'getDeliveredItems']);
        Route::post('/save',   [Controllers\Api\TradingController::class, 'saveDeliveredItems']);
        Route::post('/finish', [Controllers\Api\TradingController::class, 'finishDelivery']);
    });
});
