<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\checkAdmin;
use App\Http\Middleware\EnsureProfileComplete;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
            }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias(['EnsureProfileComplete' => EnsureProfileComplete::class]);
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias(['CheckAdmin' => checkAdmin::class]);
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias(['cors' => HandleCors::class]);
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias(['EnsureEmailIsVerified ' => EnsureEmailIsVerified::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();