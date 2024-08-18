<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use App\Http\Middleware\checkAdmin;
use App\Http\Middleware\EnsureProfileComplete;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(AuthenticationController::class)->group(function () {
    Route::post('authenticate', 'authenticate');
    Route::post('forget/password', 'forgetPassword');
    Route::post('password/reset/{token}/{email}', 'resetPassword')->name('password.reset');
    Route::post('logout', 'logout')->middleware('auth:sanctum');
});

Route::controller(ServiceController::class)->group(function () {
    Route::get('get/service/{id}', 'getService');
    Route::get('all/services', 'getAllServices');
});

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::controller(ProfileController::class)->group(function () {
        Route::get('user/profile', 'show');
        Route::post('update/profile', 'updateProfile');
        Route::post('/user/delete/account', 'deleteAccount');
    });

    Route::middleware(checkAdmin::class)->group(function () {
        Route::controller(ServiceController::class)->group(function () {
            Route::get('services/provider/{user_id}', 'getServicesByProvider');
            Route::post('create/service', 'createService');
            Route::post('update/service/{id}', 'updateService');// theres a question for sir on this function
            Route::delete('delete/service/{id}', 'deleteService');
        });
    });

    Route::middleware(EnsureProfileComplete::class)->group(function () {
        Route::controller(ServiceController::class)->group(function () {

        });
    });
});

Route::controller(CategoryController::class)->group(function () {

});