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

Route::get('menu', [MenuController::class, 'index']);
Route::post('order', [OrderController::class, 'store']);
// Route::apiResource();
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
