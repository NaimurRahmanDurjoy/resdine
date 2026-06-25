<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\SoftwareMenu;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $roles = Role::with('landingMenu')
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%"))
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction,
            ],
            'pageTitle' => 'Manage Roles',
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Roles/Form', [
            'isEdit' => false,
            'landingMenus' => SoftwareMenu::whereNotNull('route')->get(),
            'pageTitle' => 'Create Role',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string|max:500',
            'status' => 'required|boolean',
            'landing_menu_id' => 'nullable|exists:software_menus,id',
        ]);

        Role::create($data);

        return redirect()->route('admin.settings.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        return Inertia::render('Admin/Roles/Form', [
            'role' => $role,
            'isEdit' => true,
            'landingMenus' => SoftwareMenu::whereNotNull('route')->get(),
            'pageTitle' => 'Edit Role',
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:500',
            'status' => 'required|boolean',
            'landing_menu_id' => 'nullable|exists:software_menus,id',
        ]);
        $role->update($data);

        return redirect()->route('admin.settings.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        // Check if role has users assigned
        $userCount = User::where('role_id', $role->id)->count();
        if ($userCount > 0) {
            return redirect()->back()->with('error', "Cannot delete role while it has {$userCount} user(s) assigned.");
        }

        $role->delete();
        return redirect()->route('admin.settings.roles.index')->with('success', 'Role deleted successfully.');
    }

    public function permissions(Role $role, \App\Services\PermissionService $permissionService)
    {
        // Get structured software menus
        $softwareMenus = $permissionService->getPermissionTree();

        // Get currently allowed action IDs for this role
        $rolePermissions = DB::table('role_permissions')
            ->where('role_id', $role->id)
            ->where('is_allowed', true)
            ->pluck('software_menu_action_id')
            ->toArray();

        return Inertia::render('Admin/Roles/Permissions', [
            'role' => $role,
            'softwareMenus' => $softwareMenus,
            'rolePermissions' => $rolePermissions,
            'pageTitle' => 'Manage Permissions: ' . $role->name,
        ]);
    }

    public function updatePermissions(Request $request, Role $role, \App\Services\PermissionService $permissionService)
    {
        $request->validate([
            'allowed_action_ids' => 'present|array',
            'allowed_action_ids.*' => 'exists:software_menu_actions,id',
        ]);

        $permissionService->updateRolePermissions($role, $request->allowed_action_ids);

        return redirect()->back()->with('success', 'Role permissions updated successfully.');
    }
}
