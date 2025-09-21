<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     */
    public const HOME = '/home';

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        $this->routes(function () {
            // Web routes
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // Admin routes
            Route::middleware(['web', 'auth'])
                ->group(base_path('routes/admin.php'));

            // API routes (optional)
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));
        });
    }
}
