<?php

namespace App\Http\Controllers\DevAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Branch;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return view('devAdmin.users.index', compact('users', 'search', 'sort', 'direction'));
    }

        public function create()
    {
        $branch = Branch::all();
        return view('devAdmin.users.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->validate([

        ]);

        User::create($request->all());
        // $this->menuService->clearAllCache();

        return redirect()->route('devAdmin.users.index')->with('success', 'Users created successfully.');
    }

    public function edit(User $User)
    {
        $parents = User::whereNull('parent_id')->where('id', '!=', $User->id)->get();
        return view('devAdmin.users.edit', compact('User', 'parents'));
    }

    public function update(Request $request, User $User)
    {
        $request->validate([

        ]);

        $User->update($request->all());
        // $this->menuService->clearAllCache();

        return redirect()->route('devAdmin.users.index')->with('success', 'Users updated successfully.');
    }

    public function destroy(User $User)
    {
        $User->delete();
        // $this->menuService->clearAllCache();

        return redirect()->back()->with('success', 'Users deleted successfully.');
    }
}
