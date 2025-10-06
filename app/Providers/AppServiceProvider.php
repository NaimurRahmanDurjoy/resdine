<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Pagination\Paginator;
use App\Services\MenuService;

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

            if (Auth::check()) {
                $menus = app(MenuService::class)->getForUser(Auth::user());
            }

            $view->with('menus', $menus);
        });
        
    }
}
