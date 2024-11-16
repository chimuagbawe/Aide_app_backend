<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('redirect/profile', function(){
//     return view('profile');
// })->name('profile');