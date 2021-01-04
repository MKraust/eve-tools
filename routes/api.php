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

Route::get('/production/modules/search',    [Controllers\Api\ProductionController::class, 'searchModules']);
Route::get('/production/favorites/list',    [Controllers\Api\ProductionController::class, 'getFavorites']);
Route::post('/production/favorites/add',    [Controllers\Api\ProductionController::class, 'addFavorite']);
Route::post('/production/favorites/delete', [Controllers\Api\ProductionController::class, 'deleteFavorite']);
