<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Pagination\Paginator;
use App\Services\MenuService;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\Payments\PaymentManager::class, function ($app) {
            return new \App\Services\Payments\PaymentManager($app);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Authenticate::redirectUsing(function ($request) {
            if ($request->is('devAdmin') || $request->is('devAdmin/*')) {
                return route('devAdmin.login');
            }

            return route('admin.login');
        });

        Paginator::useTailwind();
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
    }
}
