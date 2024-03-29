<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\Auth\LoginController;

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
Route::post('login', [LoginController::class, 'login']);

    // authenticated user only
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('logout', [LoginController::class, 'logout']);

    //menus
    Route::get('menu', [MenuController::class, 'index']);
    Route::get('menu_all', [MenuController::class, 'indexall']);
    Route::post('create_menu', [MenuController::class, 'store']);
    Route::put('update_menu/{menu}', [MenuController::class, 'update']);
    Route::patch('update_menu_status/{menu}', [MenuController::class, 'menuStatus']);

    //employees
    Route::get('employees', [EmployeeController::class, 'index']);
    Route::get('employees_all', [EmployeeController::class, 'indexall']);
    Route::post('create_employee', [EmployeeController::class, 'store']);
    Route::put('update_employee/{employee}', [EmployeeController::class, 'update']);
    Route::patch('update_employee_status/{employee}', [EmployeeController::class, 'employeeStatus']);

    // Route::delete('delete_employee/{employee}', [EmployeeController::class, 'destroy']);

    //orders
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('show_order/{order}', [OrderController::class, 'show']);
    Route::post('create_order', [OrderController::class, 'store']);
    Route::put('update_order/{order}', [OrderController::class, 'update']);
    Route::put('update_item/{item}', [OrderController::class, 'updateItem']);

    // get employee items for order
    Route::get('get_employee_orders/{order}', [OrderController::class, 'employeeOrders']);




    // send order
    Route::post('send_order', [OrderController::class, 'send']);

    //reports
    Route::get('order_report', [ReportController::class, 'index']);
    Route::get('items_report', [ReportController::class, 'itemsReport']);


});

