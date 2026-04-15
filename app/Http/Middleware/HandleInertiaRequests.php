<?php

namespace App\Http\Middleware;

use App\Services\DevAdminMenuService;
use App\Services\MenuService;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Auth;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        $isAdminRoute = $request->is('admin') || $request->is('admin/*');
        $isDevAdminRoute = $request->is('devAdmin') || $request->is('devAdmin/*');

        $menus = [];
        $user = null;

        if ($isAdminRoute && Auth::guard('web')->check()) { // Assuming 'web' guard for admin based on your setup
            $user = Auth::guard('web')->user();
            $menuService = app(MenuService::class);
            $menus = $menuService->prepareForView($menuService->getMenusFor($user), $user);
            $notifications = (new \App\Services\NotificationService())->getAlerts();
        } elseif ($isDevAdminRoute && Auth::guard('admin')->check()) { // Assuming 'admin' guard for devAdmin based on your setup
            $user = Auth::guard('admin')->user();
            $menuService = app(DevAdminMenuService::class);
            $menus = $menuService->prepareForView($menuService->getMenusFor($user), $user);
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user,
            ],
            'notifications' => $notifications,
            'menus' => $menus,
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ]);
    }
}
