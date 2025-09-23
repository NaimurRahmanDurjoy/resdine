<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
                        // Manually register your 'role' middleware
            app('router')->aliasMiddleware('role', RoleMiddleware::class);

            // Admin routes
            Route::prefix('admin')
                ->middleware(['web', 'auth', 'role:admin'])
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
            // Admin routes
            Route::middleware(['web'])->group(base_path('routes/admin.php'));

        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();

