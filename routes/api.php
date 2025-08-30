<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\CheckoutController;
use App\Http\Controllers\API\UserActivityController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [AuthController::class, 'login']);

// \App\Http\Middleware\UpdateUserActivity,
// implement this middleware on below api
Route::middleware(['auth:sanctum', \App\Http\Middleware\UpdateUserActivity::class])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // cart api
    Route::get('cart', [CartController::class, 'index']);
    Route::post('cart/add', [CartController::class, 'addItem']);

    Route::get('checkout', [CheckoutController::class, 'show']);
    Route::post('checkout', [CheckoutController::class, 'process']);

    // User activity routes
    Route::get('user/activity', [UserActivityController::class, 'show']);
    Route::get('user/login-duration', [UserActivityController::class, 'getLoginDuration']);
    Route::get('user/login-durations', [UserActivityController::class, 'getLoginDurations']);
    // Route::get('user/online-duration', [UserActivityController::class, 'getOnlineDuration']);
});

// Separate group for the excluded route
Route::middleware('auth:sanctum')->group(function () {
    Route::get('user/online-duration', [UserActivityController::class, 'getOnlineDuration']);
});
