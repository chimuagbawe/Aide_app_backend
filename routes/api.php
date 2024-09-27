<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\bookingsController;
use App\Http\Controllers\serviceProviderController;
use Illuminate\Http\Request;
use App\Http\Middleware\checkAdmin;
use App\Http\Middleware\EnsureProfileComplete;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Middleware\HandleCors;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(AuthenticationController::class)->group(function () {
    Route::post('/auth', 'authenticate');
    Route::get('/auth/{client}','redirect');
    Route::get('/auth/{client}/callback','handleCallback');
    Route::post('/forget/password', 'forgetPassword');
    Route::get('/email/verify/{id}/{hash}', 'verify')->name('verification.verify');
    Route::post('/password/reset/{token}/{email}', 'resetPassword')->name('password.reset');
});

Route::controller(ServiceController::class)->group(function () {
    Route::get('/get/service/{id}', 'getService');
    Route::get('/all/services', 'getAllServices');
});
Route::post('/user/delete/account', [ProfileController::class, 'deleteAccount']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/email/resend', [AuthenticationController::class, 'resend']);

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/user/profile', 'show');
        Route::post('/logout', 'logout');
        Route::post('/update/profile', 'updateProfile');
    });

Route::middleware(EnsureEmailIsVerified::class)->group(function () {

    Route::middleware(checkAdmin::class)->group(function () {
        Route::controller(ServiceController::class)->group(function () {
            Route::get('/services/provider/{user_id}', 'getServicesByProvider');
            Route::post('/create/service', 'createService');
            Route::post('/update/service/{id}', 'updateService');
            Route::delete('/delete/service/{id}', 'deleteService');
        });
    });

    Route::controller(serviceProviderController::class)->group(function () {
        Route::post('/review', 'store');
        Route::post('/create/service/provider', 'createServiceProvider');
        Route::get('/service/provider/reviews/{serviceProviderId}', 'getReviewsByProvider');
        Route::post('/create/service/provider/availability', 'createServiceProviderAvailability');
    });

    Route::controller(bookingsController::class)->group(function () {

        Route::post('/book', 'store');
        Route::patch('/bookings/{id}/update', 'update');
        Route::patch('/bookings/{id}/cancel', 'cancelBooking');
        Route::patch('/bookings/{id}/confirm', 'confirmBooking');


    });



});

});