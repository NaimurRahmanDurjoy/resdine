<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortable = ['name','type','price','status','created_at'];
        $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'created_at';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';
        $perPage = min($request->input('perPage', 10), 100);
        $events = Event::with('branch')
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();
        return Inertia::render('Admin/Reservations/Events/Index', [
            'events' => $events,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction,
                'perPage' => $perPage
            ],
            'pageTitle' => 'Company Events'
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Reservations/Events/Create', [
            'branches' => Branch::all(),
            'pageTitle' => 'Schedule Event'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'estimated_guests' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        Event::create($validated);

        return redirect()->route('admin.events.index')->with('success', 'Event scheduled. Waiting for admin approval.');
    }

    public function approve(Request $request, Event $event)
    {
        $event->update(['admin_approval' => $request->approval]); // 1=approved, 2=rejected
        
        $msg = $request->approval == 1 ? 'Event approved. Branch marked as fully booked for this time.' : 'Event rejected.';
        
        return back()->with('success', $msg);
    }
}
