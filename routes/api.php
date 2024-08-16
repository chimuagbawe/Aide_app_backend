<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\EnsureProfileComplete;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(AuthenticationController::class)->group(function () {
    Route::post('auth', 'authenticate');
    Route::post('forget/password', 'forgetPassword');
    Route::put('update/password', 'updatePassword');
    Route::post('logout', 'logout')->middleware('auth:sanctum');
});

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::controller(ProfileController::class)->group(function () {
        Route::get('user/profile', 'show');
        Route::put('update/profile', 'updateProfile');
        Route::delete('/user/delete/account', 'deleteAccount');
    });

    Route::middleware(EnsureProfileComplete::class)->group(function () {
        Route::controller(ServiceController::class)->group(function () {
            Route::post('create/service', 'createService');
            Route::put('update/service/{id}', 'updateService');
            Route::delete('delete/service/{id}', 'deleteService');
        });
    });

    Route::middleware(CheckAdmin::class)->group(function () {
        Route::controller(CategoryController::class)->group(function () {
            Route::post('create/category', 'createCategory');
            Route::put('update/category/{id}', 'updateCategory');
            Route::delete('delete/category/{id}', 'deleteCategory');
        });
    });

});

Route::controller(ServiceController::class)->group(function () {
    Route::get('all/services', 'getAllServices');
    Route::get('get/service/{id}', 'getService');
    Route::get('services/category/{category_id}', 'getServicesByCategory');
    Route::get('services/provider/{user_id}', 'getServicesByProvider');
});

Route::controller(CategoryController::class)->group(function () {
    Route::get('all/categories', 'getAllCategories');
    Route::get('get/category/{id}', 'getCategory');
});