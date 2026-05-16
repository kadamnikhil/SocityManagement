<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeController;

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

Route::middleware(['basic_auth'])->group(function () {
    Route::namespace('Api')->name('api.')->group(function () {
        //App Version Check
         Route::post('get-app-version', [AuthController::class, 'getAppVersion'])->name('get-app-version');

        // Authentication
        Route::post('register',[AuthController::class,'register'])->name('register');
        Route::post('verify-otp',[AuthController::class,'verifyOTP'])->name('verify-otp');
        Route::post('login',[AuthController::class,'login'])->name('login');
        Route::post('resend-otp',[AuthController::class,'resendOTP'])->name('resend-otp');

        Route::middleware(['token'])->group(function () {
            // Users
            Route::post('user',[AuthController::class,'getUser'])->name('user');
            Route::post('user/update',[AuthController::class,'updateUser'])->name('user.update');

            //sliders
            Route::post('sliders',[HomeController::class,'getSliders'])->name('sliders');

            //RazorPay
            Route::post('/new-order', [HomeController::class, 'newOrder'])->name('new-order');
            Route::post('/payment-success', [HomeController::class, 'paymentSuccess'])->name('payment-success');
        });
    });
});
