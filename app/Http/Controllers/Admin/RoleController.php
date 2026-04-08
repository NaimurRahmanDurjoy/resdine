<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\SoftwareMenu;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $roles = Role::query()
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
            'pageTitle' => 'Create Role',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string|max:500',
            'status' => 'required|boolean',
        ]);

        Role::create($data);

        return redirect()->route('admin.settings.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        return Inertia::render('Admin/Roles/Form', [
            'role' => $role,
            'isEdit' => true,
            'pageTitle' => 'Edit Role',
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:500',
            'status' => 'required|boolean',
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

    public function permissions(Role $role)
    {
        // Get all software menus with their actions and recursive children
        $softwareMenus = SoftwareMenu::whereNull('parent_id')
            ->with(['childrenRecursive.actions', 'actions'])
            ->orderBy('order')
            ->get();

        // Get currently allowed action IDs for this role
        $rolePermissions = RolePermission::where('role_id', $role->id)
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

    public function updatePermissions(Request $request, Role $role)
    {
        $request->validate([
            'allowed_action_ids' => 'present|array',
            'allowed_action_ids.*' => 'exists:software_menu_actions,id',
        ]);

        // Remove all current permissions for this role
        RolePermission::where('role_id', $role->id)->delete();

        // Add new permissions
        foreach ($request->allowed_action_ids as $actionId) {
            RolePermission::create([
                'role_id' => $role->id,
                'software_menu_action_id' => $actionId,
                'is_allowed' => true,
            ]);
        }

        // Clear permission cache for all users of this role
        User::where('role_id', $role->id)->chunk(100, function ($users) {
            foreach ($users as $user) {
                Cache::forget("perm_{$user->id}");
            }
        });

        return redirect()->back()->with('success', 'Role permissions updated successfully.');
    }
}
