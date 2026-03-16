<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Membership;
use App\Models\LoyaltyPoint;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller {
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortable = ['name', 'type', 'price', 'status', 'created_at'];
        $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'created_at';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';
        $perPage = min($request->input('perPage', 10), 100);
        $customers = Customer::with(['memberships', 'loyaltyPoints'])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Admin/Customers/Index', [
            'customers' => $customers,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction,
                'perPage' => $perPage
            ],
            'pageTitle' => 'Customers'
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Customers/Create', [
            'memberships' => Membership::where('status', 1)->get(),
            'pageTitle' => 'Add Customer'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|integer',
            'membership_id' => 'nullable|exists:memberships,id',
            'card_no' => 'required_with:membership_id|nullable|string|max:50'
        ]);

        $customer = Customer::create($validated);

        if ($request->membership_id) {
            $customer->memberships()->attach($request->membership_id, [
                'card_no' => $request->card_no,
                'start_date' => now(),
            ]);
        }

        LoyaltyPoint::create(['customer_id' => $customer->id]);

        return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        return Inertia::render('Admin/Customers/Edit', [
            'customer' => $customer->load('memberships'),
            'memberships' => Membership::where('status', 1)->get(),
            'pageTitle' => 'Edit Customer'
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|integer',
            'membership_id' => 'nullable|exists:memberships,id',
            'card_no' => 'required_with:membership_id|nullable|string|max:50'
        ]);

        $customer->update($validated);

        if ($request->membership_id) {
            $customer->memberships()->syncWithPivotValues([$request->membership_id], [
                'card_no' => $request->card_no,
                'start_date' => now(),
            ]);
        } else {
            $customer->memberships()->detach();
        }

        return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully.');
    }
}


