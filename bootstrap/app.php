<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\checkAdmin;
use App\Http\Middleware\EnsureProfileComplete;

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
        // $middleware->append(EnsureProfileComplete::class);
        $middleware->alias(['EnsureProfileComplete' => EnsureProfileComplete::class]);
    })
    ->withMiddleware(function (Middleware $middleware) {
        // $middleware->append(CheckAdmin::class);
        $middleware->alias(['CheckAdmin' => checkAdmin::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();