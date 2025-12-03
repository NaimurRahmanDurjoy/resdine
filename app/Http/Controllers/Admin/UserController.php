<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $users = User::query()
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%"))
            ->orderBy($sort, $direction)
            ->paginate(10);

        return view('admin.users.index', compact('users', 'search', 'sort', 'direction'));
    }
    public function create()
    {
        $user = new User();
        return view('admin.users.create', compact('user'));
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email',
            'status' => 'required|boolean',
        ]);

        $user = User::create($data);

        if ($request->ajax()) {
            return response()->json(['message' => 'User created successfully', 'user' => $user]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }
    public function show($id) {}
    public function edit($id) {}
    public function update(Request $request, $id) {}
    public function destroy($id) {}
}
