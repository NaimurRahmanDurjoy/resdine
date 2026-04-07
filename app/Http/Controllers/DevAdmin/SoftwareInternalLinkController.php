<?php

namespace App\Http\Controllers\DevAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SoftwareMenuActionRequest;
use App\Models\SoftwareMenu;
use App\Models\SoftwareMenuAction;
use App\Services\DevAdminMenuService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SoftwareInternalLinkController extends Controller
{
    protected DevAdminMenuService $menuService;

    public function __construct(DevAdminMenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'id');
        $direction = $request->get('direction', 'desc');

        $actions = SoftwareMenuAction::with('softwareMenu')
            ->when($search, function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('route', 'like', "%{$search}%")
                    ->orWhereHas('softwareMenu', fn($m) => $m->where('name', 'like', "%{$search}%"));
            })
            ->orderBy($sort, $direction)
            ->paginate(15);

        return Inertia::render('DevAdmin/SystemConfig/SoftwareInternalLink/Index', [
            'items' => $actions,
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function create()
    {
        $menus = SoftwareMenu::where('is_active', true)->orderBy('name')->get();
        return Inertia::render('DevAdmin/SystemConfig/SoftwareInternalLink/Form', [
            'menus' => $menus,
            'isEdit' => false
        ]);
    }

    public function store(SoftwareMenuActionRequest $request)
    {
        $validated = $request->validated();

        // Get the actions as array (handles both single string and array)
        $actions = $request->getActions();

        // Determine the base route
        $baseRoute = SoftwareMenuAction::generateRouteName($validated['route'], '');
        $baseRoute = rtrim($baseRoute, '.'); // Remove trailing dot if any

        // Create a separate record for each action
        foreach ($actions as $action) {
            // Generate the appropriate route for this action using Model helper
            $route = SoftwareMenuAction::generateRouteName($baseRoute, $action);

            SoftwareMenuAction::updateOrCreate(
                [
                    'software_menu_id' => $validated['software_menu_id'],
                    'action' => $action,
                ],
                [
                    'route' => $route,
                    'is_active' => $validated['is_active'] ?? true,
                ]
            );
        }

        $this->menuService->clearCache(Auth::guard('admin')->user());

        return redirect()->route('devAdmin.systemConfig.software.internal-link.index')
            ->with('success', count($actions) . ' menu action(s) created successfully.');
    }

    public function edit(SoftwareMenuAction $internalLink)
    {
        $menus = SoftwareMenu::where('is_active', true)->orderBy('name')->get();
        return Inertia::render('DevAdmin/SystemConfig/SoftwareInternalLink/Form', [
            'action' => $internalLink->load('softwareMenu'),
            'menus' => $menus,
            'isEdit' => true
        ]);
    }

    public function update(SoftwareMenuActionRequest $request, SoftwareMenuAction $internalLink)
    {
        $validated = $request->validated();

        // Get the actions as array
        $actions = $request->getActions();
        $action = current($actions);

        // Determine the base route
        // If the route provided already has a suffix, let's extract the base
        $segments = explode('.', $validated['route']);
        if (in_array(end($segments), SoftwareMenuAction::ROUTE_MAP)) {
            array_pop($segments);
        }
        $baseRoute = implode('.', $segments);

        $route = SoftwareMenuAction::generateRouteName($baseRoute, $action);

        $internalLink->update([
            'action' => $action,
            'route' => $route,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $this->menuService->clearCache(Auth::guard('admin')->user());

        return redirect()->route('devAdmin.systemConfig.software.internal-link.index')
            ->with('success', 'Menu action updated successfully.');
    }

    public function destroy(SoftwareMenuAction $internalLink)
    {
        $internalLink->delete();
        $this->menuService->clearCache(Auth::guard('admin')->user());

        return redirect()->back()->with('success', 'Menu action deleted successfully.');
    }

    public function permissions(SoftwareMenuAction $internalLink)
    {
        $internalLink->load('softwareMenu');

        // All roles and their permission status for this action
        $roles = \App\Models\Role::all()->map(function ($role) use ($internalLink) {
            $role->is_allowed = \App\Models\RolePermission::where('role_id', $role->id)
                ->where('software_menu_action_id', $internalLink->id)
                ->where('is_allowed', true)
                ->exists();
            return $role;
        });

        // Users who have explicit overrides for this action
        $usersWithOverrides = \App\Models\UserPermission::with('user')
            ->where('software_menu_action_id', $internalLink->id)
            ->get()
            ->map(function ($override) {
                return [
                    'id' => $override->user_id,
                    'name' => $override->user->name,
                    'email' => $override->user->email,
                    'is_allowed' => $override->is_allowed,
                ];
            });

        return Inertia::render('DevAdmin/SystemConfig/SoftwareInternalLink/Permissions', [
            'action' => $internalLink,
            'roles' => $roles,
            'usersWithOverrides' => $usersWithOverrides,
        ]);
    }

    public function updatePermissions(Request $request, SoftwareMenuAction $internalLink)
    {
        $request->validate([
            'roles' => 'array', // [role_id => is_allowed]
            'users' => 'array', // [{user_id: id, is_allowed: boolean}]
        ]);

        // 1. Update Role Permissions
        if ($request->has('roles')) {
            foreach ($request->roles as $roleId => $isAllowed) {
                \App\Models\RolePermission::updateOrCreate(
                    ['role_id' => $roleId, 'software_menu_action_id' => $internalLink->id],
                    ['is_allowed' => $isAllowed]
                );

                // Clear cache for all users of this role
                \App\Models\User::where('role_id', $roleId)->chunk(100, function ($users) {
                    foreach ($users as $user) {
                        \Illuminate\Support\Facades\Cache::forget("perm_{$user->id}");
                    }
                });
            }
        }

        // 2. Update User Overrides
        if ($request->has('users')) {
            // Remove existing overrides for these users for this action
            $userIds = collect($request->users)->pluck('user_id')->toArray();
            \App\Models\UserPermission::where('software_menu_action_id', $internalLink->id)
                ->whereIn('user_id', $userIds)
                ->delete();

            // Add new ones
            foreach ($request->users as $userOverride) {
                if ($userOverride['is_allowed'] !== null) {
                    \App\Models\UserPermission::create([
                        'user_id' => $userOverride['user_id'],
                        'software_menu_action_id' => $internalLink->id,
                        'is_allowed' => $userOverride['is_allowed'],
                    ]);

                    \Illuminate\Support\Facades\Cache::forget("perm_{$userOverride['user_id']}");
                }
            }
        }

        return redirect()->back()->with('success', 'Permissions updated successfully.');
    }
}
