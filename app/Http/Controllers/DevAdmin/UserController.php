<?php

namespace App\Http\Controllers\DevAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Branch;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $users = User::query()->with('role')
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%"))
            ->orderBy($sort, $direction)
            ->paginate(10);

        return Inertia::render('DevAdmin/Users/Index', [
            'users' => $users,
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function create()
    {
        $branches = Branch::all();
        $roles = Role::all();
        return Inertia::render('DevAdmin/Users/Form', [
            'branches' => $branches,
            'roles' => $roles,
            'isEdit' => false
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'branch_id' => 'required|exists:branches,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        User::create($request->all());

        return redirect()->route('devAdmin.users.index')->with('success', 'Users created successfully.');
    }

    public function edit(User $user)
    {
        $branches = Branch::all();
        $roles = Role::all();
        return Inertia::render('DevAdmin/Users/Form', [
            'user' => $user,
            'branches' => $branches,
            'roles' => $roles,
            'isEdit' => true
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'branch_id' => 'required|exists:branches,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        $data = $request->all();
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('devAdmin.users.index')->with('success', 'Users updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->back()->with('success', 'Users deleted successfully.');
    }
}
