<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;

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

//menus
Route::get('menu', [MenuController::class, 'index']);
Route::get('menunopg', [MenuController::class, 'indexnopg']);

Route::post('menu', [MenuController::class, 'store']);

//create orders
Route::post('order', [OrderController::class, 'store']);

// update order
Route::put('order_update/{order}', [OrderController::class, 'update']);

//open orders
Route::get('open_orders', [OrderController::class, 'index']);
Route::get('open_order/{order}', [OrderController::class, 'show']);

// send order
Route::post('send_order', [OrderController::class, 'send']);


//closed orders
Route::get('closed_orders', [OrderController::class, 'indexClosed']);
Route::get('closed_order/{order}', [OrderController::class, 'closedShow']);

// Route::apiResource();
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
