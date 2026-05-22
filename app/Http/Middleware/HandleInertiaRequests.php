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
        $notifications = [];

        if ($isAdminRoute && Auth::guard('web')->check()) { // Assuming 'web' guard for admin based on your setup
            $user = Auth::guard('web')->user();
            $menuService = app(MenuService::class);
            $menus = $menuService->prepareForView($menuService->getMenusFor($user), $user);
            
            // Format native Laravel notifications to match the frontend expectations
            $unread = $user->unreadNotifications;
            
            $items = $unread->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'type' => $notif->data['type'] ?? 'info',
                    'message' => $notif->data['message'] ?? '',
                    'url' => $notif->data['url'] ?? '#',
                ];
            })->toArray();

            $notifications = [
                'total' => $unread->count(),
                'groups' => [], // We can simplify or keep empty if frontend relies on items
                'items' => $items,
            ];

        } elseif ($isDevAdminRoute && Auth::guard('admin')->check()) { // Assuming 'admin' guard for devAdmin based on your setup
            $user = Auth::guard('admin')->user();
            $menuService = app(DevAdminMenuService::class);
            $menus = $menuService->prepareForView($menuService->getMenusFor($user), $user);

            // Format native Laravel notifications for DevAdmin
            $unread = $user->unreadNotifications;
            $items = $unread->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'type' => $notif->data['type'] ?? 'info',
                    'message' => $notif->data['message'] ?? '',
                    'url' => $notif->data['url'] ?? '#',
                ];
            })->toArray();

            $notifications = [
                'total' => $unread->count(),
                'groups' => [],
                'items' => $items,
            ];
        }

        $activeBranchId = null;
        $canSelectBranch = false;
        $branches = [];
        $branchSetting = null;

        if ($user) {
            $activeBranchId = method_exists($user, 'getActiveBranchId') ? $user->getActiveBranchId() : null;
            $canSelectBranch = $user->role && strtolower($user->role->name ?? '') === 'admin';
            if ($canSelectBranch) {
                $branches = \App\Models\Branch::where('status', 1)->get(['id', 'name']);
            }
        }

        if (!$activeBranchId) {
            $activeBranchId = \App\Models\Branch::first()?->id;
        }

        if ($activeBranchId) {
            $branchSetting = \App\Models\BranchSetting::with('currency')->where('branch_id', $activeBranchId)->first();
        }

        if (!$branchSetting) {
            $branchSetting = new \App\Models\BranchSetting([
                'currency_id' => null,
                'vat_registration_no' => null,
                'vat_percentage' => 0.00,
                'service_charge_percentage' => 0.00,
                'is_vat_inclusive' => false,
                'timezone' => 'UTC',
                'language_code' => 'en',
                'invoice_prefix' => 'INV',
            ]);
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user,
            ],
            'business' => [
                'currency_symbol' => $branchSetting->currency?->symbol ?? '$',
                'active_branch_id' => $activeBranchId,
                'can_select_branch' => $canSelectBranch,
                'branches' => $branches,
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
