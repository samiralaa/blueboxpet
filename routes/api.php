<?php

use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DrugsController;
use App\Http\Controllers\GovernorateController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SubDiliveryController;
use App\Http\Controllers\TestController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CitiesPriceController;

use App\Http\Controllers\OrderController;
Route::post('updatestatus/{id}',[OrderController::class,'updateStatus']);
Route::get('get-pyment/{id}',[OrderController::class,'getPymentToUser']);
Route::get('orderuser/{id}',[OrderController::class,'orderForUser']);
Route::apiResource('orders', OrderController::class);
Route::apiResource('sub-diliveries', SubDiliveryController::class);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('data', function (Request $request) {
    return User::get();
});

Route::controller(RegisterController::class)->group(function () {
    Route::post('register', 'create');
    Route::post('login', 'login');
    Route::get('index/users', 'index');
    Route::get('show/user/{id}', 'show');

    Route::post('update/user/{id}', [RegisterController::class, 'update']);
    Route::delete('delete/user/{id}', [RegisterController::class, 'delete']);
});
Route::middleware('auth:sanctum')->post('/logout', [RegisterController::class, 'logout']);

Route::middleware('auth:sanctum')->post('/change-password', [RegisterController::class, 'changePassword']);

Route::controller(StockController::class)->group(function () {

    Route::get('index/stocks', 'index');
    Route::get('show/stock/{id}', 'show');
    Route::post('create/stock',[StockController::class, 'store']);
    Route::post('update/stock/{id}', [StockController::class, 'update']);
    Route::delete('delete/stock/{id}', [StockController::class, 'delete']);
        Route::post('update/quantity/{id}', [StockController::class, 'updateQuantity']);

});
Route::post('cities-prices/{id}',[CitiesPriceController::class,'update']);
Route::apiResource('cities-prices', CitiesPriceController::class);

Route::post('/orders/reject', [OrderController::class, 'rejectOrder']);
