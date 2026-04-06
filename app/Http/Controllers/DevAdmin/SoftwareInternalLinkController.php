<?php

namespace App\Http\Controllers\DevAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SoftwareMenuActionRequest;
use App\Models\SoftwareMenu;
use App\Models\SoftwareMenuAction;
use App\Services\DevAdminMenuService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SoftwareInternalLinkController extends Controller
{
    protected DevAdminMenuService $menuService;

    public function __construct(DevAdminMenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'id');
        $direction = $request->get('direction', 'desc');

        $actions = SoftwareMenuAction::with('softwareMenu')
            ->when($search, function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('route', 'like', "%{$search}%")
                    ->orWhereHas('softwareMenu', fn($m) => $m->where('name', 'like', "%{$search}%"));
            })
            ->orderBy($sort, $direction)
            ->paginate(15);

        return Inertia::render('DevAdmin/SystemConfig/SoftwareInternalLink/Index', [
            'items' => $actions,
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function create()
    {
        $menus = SoftwareMenu::where('is_active', true)->orderBy('name')->get();
        return Inertia::render('DevAdmin/SystemConfig/SoftwareInternalLink/Form', [
            'menus' => $menus,
            'isEdit' => false
        ]);
    }

    public function store(SoftwareMenuActionRequest $request)
    {
        SoftwareMenuAction::create($request->validated());
        $this->menuService->clearCache(Auth::guard('admin')->user());

        return redirect()->route('devAdmin.systemConfig.software.internalLink.index')
            ->with('success', 'Menu action created successfully.');
    }

    public function edit(SoftwareMenuAction $internalLink)
    {
        $menus = SoftwareMenu::where('is_active', true)->orderBy('name')->get();
        return Inertia::render('DevAdmin/SystemConfig/SoftwareInternalLink/Form', [
            'action' => $internalLink->load('softwareMenu'),
            'menus' => $menus,
            'isEdit' => true
        ]);
    }

    public function update(SoftwareMenuActionRequest $request, SoftwareMenuAction $internalLink)
    {
        $internalLink->update($request->validated());
        $this->menuService->clearCache(Auth::guard('admin')->user());

        return redirect()->route('devAdmin.systemConfig.software.internalLink.index')
            ->with('success', 'Menu action updated successfully.');
    }

    public function destroy(SoftwareMenuAction $internalLink)
    {
        $internalLink->delete();
        $this->menuService->clearCache(Auth::guard('admin')->user());

        return redirect()->back()->with('success', 'Menu action deleted successfully.');
    }
}
