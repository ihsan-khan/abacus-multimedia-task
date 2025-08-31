<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\CheckoutController;
use App\Http\Controllers\API\UserActivityController;
use App\Http\Controllers\API\OnlineDurationController;

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

Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'activity.update', 'activity.track'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // cart api
    Route::get('cart', [CartController::class, 'index']);
    Route::post('cart/add', [CartController::class, 'addItem']);

    // checkout api
    Route::get('checkout', [CheckoutController::class, 'show']);
    Route::post('checkout', [CheckoutController::class, 'process']);

    // User activity routes
    Route::get('user/login-duration', [UserActivityController::class, 'getLoginDuration']);
    Route::get('user/login-durations', [UserActivityController::class, 'getLoginDurations']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('user/online-duration', [OnlineDurationController::class, 'getOnlineStats']);
    // Route::post('/end-session', [OnlineDurationController::class, 'endSession']);
});
