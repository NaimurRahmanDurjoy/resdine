<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();
        View::composer('layouts.sidebar', function ($view) {
            $menus = [];

            if (Auth::check()) { // Make sure user is logged in
                $menus = Cache::remember('user_menus_' . Auth::id(), 3600, function () {
                    return Auth::user()->accessibleMenus();
                });
            }

            $view->with('menus', $menus);
        });

        
    }
}
