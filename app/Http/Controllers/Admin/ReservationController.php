<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\RestaurantTable;
use App\Models\Customer;
use App\Models\Branch;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReservationController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortable = ['name','type','price','status','created_at'];
        $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'created_at';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';
        $perPage = min($request->input('perPage', 10), 100);
        $reservations = Reservation::with(['customer', 'table', 'branch'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('customer', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })->orWhereHas('table', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('branch', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();
        return Inertia::render('Admin/Reservations/Index', [
            'reservations' => $reservations,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction,
                'perPage' => $perPage
            ],
            'pageTitle' => 'Table Reservations'
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Reservations/Create', [
            'tables' => RestaurantTable::all(),
            'customers' => Customer::all(),
            'branches' => Branch::all(),
            'pageTitle' => 'New Reservation'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_id' => 'required|exists:restaurant_tables,id',
            'branch_id' => 'required|exists:branches,id',
            'reservation_time' => 'required|date',
            'guests_count' => 'required|integer|min:1',
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required_without:customer_id',
            'customer_phone' => 'required_without:customer_id',
            'special_requests' => 'nullable|string|max:500',
        ]);

        if (!$this->bookingService->isTableAvailable($request->table_id, $request->reservation_time)) {
            return back()->withErrors(['table_id' => 'This table is already booked or the branch is fully booked for an event.']);
        }

        Reservation::create($validated);

        return redirect()->route('admin.reservations.index')->with('success', 'Reservation created successfully.');
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        $reservation->update(['status' => $request->status]);
        return back()->with('success', 'Status updated.');
    }
}
