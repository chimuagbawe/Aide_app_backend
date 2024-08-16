<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(AuthenticationController::class)->group(function () {
    Route::post('authenticate', 'authenticate');
    Route::post('forget/password', 'forgetPassword');
    Route::post('update/password', 'updatePassword');
    Route::post('logout', 'logout')->middleware('auth:sanctum');
});

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::controller(ProfileController::class)->group(function () {
        Route::get('user/profile', 'show');
        Route::get('update/profile', 'updateProfile');
        Route::delete('/user/delete/account', 'deleteAccount');
    });

});