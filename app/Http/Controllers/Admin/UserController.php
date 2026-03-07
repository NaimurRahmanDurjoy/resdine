<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
            
        $data['filters'] = ['search' => $search, 'sort' => $sort, 'direction' => $direction, ];
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

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully');
    }
}
