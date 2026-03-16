<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Membership;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MembershipController extends Controller {
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortable = ['name','type','price','status','created_at'];
        $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'created_at';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';
        $perPage = min($request->input('perPage', 10), 100);
        $memberships = Membership::when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%");
        })->orderBy($sort, $direction)->paginate($perPage);
        
    return Inertia::render('Admin/Memberships/Index', [
            'memberships' => $memberships,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction,
                'perPage' => $perPage
            ],

            'pageTitle' => 'Membership Tiers'
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Memberships/Create', [
            'pageTitle' => 'Create Membership Tier'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'loyalty_multiplier' => 'required|integer|min:1',
            'min_points' => 'required|integer|min:0',
            'max_points' => 'nullable|integer|gt:min_points',
            'status' => 'required|integer'
        ]);

        Membership::create($validated);

        return redirect()->route('admin.memberships.index')->with('success', 'Membership tier created successfully.');
    }

    public function edit(Membership $membership)
    {
        return Inertia::render('Admin/Memberships/Edit', [
            'membership' => $membership,
            'pageTitle' => 'Edit Membership Tier'
        ]);
    }

    public function update(Request $request, Membership $membership)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'loyalty_multiplier' => 'required|integer|min:1',
            'min_points' => 'required|integer|min:0',
            'max_points' => 'nullable|integer|gt:min_points',
            'status' => 'required|integer'
        ]);

        $membership->update($validated);

        return redirect()->route('admin.memberships.index')->with('success', 'Membership tier updated successfully.');
    }

    public function destroy(Membership $membership)
    {
        $membership->delete();
        return redirect()->route('admin.memberships.index')->with('success', 'Membership tier deleted successfully.');
    }
}


