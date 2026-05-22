<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        channels: __DIR__.'/../routes/channels.php',
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::prefix('admin')->group(base_path('routes/admin.php'));
            Route::prefix('devAdmin')->group(base_path('routes/devAdmin.php'));
        }
    )
    ->withMiddleware(function ($middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \App\Http\Middleware\ActivityLogMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'payment/callback/*',
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        ]);
    })
    ->withExceptions(function ($exceptions) {
        $exceptions->reportable(function (\Throwable $e) {
            // Filter out common HTTP exceptions, Validation exceptions, and Auth exceptions
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface || 
                $e instanceof \Illuminate\Validation\ValidationException ||
                $e instanceof \Illuminate\Auth\AuthenticationException) {
                return;
            }

            try {
                $devAdmins = \App\Models\Admin::all();
                
                if ($devAdmins->isNotEmpty()) {
                    \Illuminate\Support\Facades\Notification::send(
                        $devAdmins, 
                        new \App\Notifications\AdminAlert(
                            'system_error', 
                            'System Error: ' . substr($e->getMessage(), 0, 150), 
                            '#' // Link to logs page if one exists
                        )
                    );
                }
            } catch (\Throwable $err) {
                // Failsafe: if the database is dead, don't crash the error handler
            }
        });
    })
    ->create();
