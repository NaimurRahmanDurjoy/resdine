<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\User;
use App\Models\SoftwareMenu;
use App\Models\UserPermission;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $data['users'] = User::query()->with('role')
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%")->orWhere('email', 'like', "%$search%"))
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        $data['filters'] = ['search' => $search, 'sort' => $sort, 'direction' => $direction,];
        $data['pageTitle'] = 'Manage Users';

        return Inertia::render('Admin/Users/Index', $data);
    }

    public function create()
    {
        $data['roles'] = Role::all();
        $data['branches'] = Branch::all();
        $data['pageTitle'] = 'Create User';

        return Inertia::render('Admin/Users/Create', $data);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|boolean',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        $data['user'] = $user;
        $data['roles'] = Role::all();
        $data['branches'] = Branch::all();
        $data['pageTitle'] = 'Edit User';

        return Inertia::render('Admin/Users/Edit', $data);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
    }

    public function profile()
    {
        $user = Auth::user()->load('profile');

        return Inertia::render('Admin/Users/Profile', [
            'user' => $user,
            'pageTitle' => 'My Profile',
        ]);
    }

    public function updateProfile(Request $request, ImageUploadService $imageUploadService)
    { 
        $user = Auth::user();

        $userData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $profileData = $request->validate([
            'address' => 'nullable|string|max:1000',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $user->update($userData);

        $profile = $user->profile;

        if ($request->hasFile('profile_photo')) {
            if ($profile && $profile->profile_photo) {
                $imageUploadService->delete($profile->profile_photo);
            }
            $profileData['profile_photo'] = $imageUploadService->upload($request->file('profile_photo'), 'users');
        }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            array_filter($profileData, fn($value) => $value !== null)
        );

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'The provided current password does not match.']);
        }

        $user->update(['password' => Hash::make($request->input('password'))]);

        return back()->with('success', 'Password updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully');
    }

    /**
     * Manage User Specific Permissions (Overrides)
     */
    public function permissions(User $user)
    {
        $user->load('role');
        $softwareMenus = SoftwareMenu::where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('order')
            ->with(['actions', 'childrenRecursive'])
            ->get();

        // Get role-based permissions
        $rolePermissions = RolePermission::where('role_id', $user->role_id)
            ->pluck('software_menu_action_id')
            ->toArray();

        // Get user overrides
        $overrides = UserPermission::where('user_id', $user->id)
            ->get()
            ->map(function ($o) {
                return [
                    'action_id' => $o->software_menu_action_id,
                    'is_allowed' => $o->is_allowed,
                ];
            })->toArray();

        return Inertia::render('Admin/Users/Permissions', [
            'user' => $user,
            'softwareMenus' => $softwareMenus,
            'rolePermissions' => $rolePermissions,
            'overrides' => $overrides,
            'pageTitle' => 'User Permissions Overrides: ' . $user->name,
        ]);
    }

    /**
     * Update User Specific Permissions (Overrides)
     */
    public function updatePermissions(Request $request, User $user)
    {
        $overrides = $request->input('overrides', []); // Array of {action_id, is_allowed}

        UserPermission::where('user_id', $user->id)->delete();

        foreach ($overrides as $override) {
            // If is_allowed is null, it means 'Inherit', so we don't save a record
            if ($override['is_allowed'] === null) continue;

            UserPermission::create([
                'user_id' => $user->id,
                'software_menu_action_id' => $override['action_id'],
                'is_allowed' => $override['is_allowed'],
            ]);
        }

        // Clear user-specific cache
        $menuService = app(\App\Services\MenuService::class);
        $menuService->clearCache($user);

        // Also clear any permission-related cache if exists
        Cache::forget("perm_{$user->id}");

        return redirect()->back()->with('success', 'User specific permissions updated successfully');
    }
}
