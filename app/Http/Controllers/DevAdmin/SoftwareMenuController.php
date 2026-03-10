<?php

namespace App\Http\Controllers\DevAdmin;

use App\Http\Controllers\Controller;
use App\Models\SoftwareMenu;
use App\Services\DevAdminMenuService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SoftwareMenuController extends Controller
{
    protected DevAdminMenuService $menuService;

    public function __construct(DevAdminMenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'order');
        $direction = $request->get('direction', 'asc');

        $menus = SoftwareMenu::with('childrenRecursive')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('route', 'like', "%{$search}%");
            })
            ->whereNull('parent_id')
            ->orderBy($sort, $direction)
            ->paginate(10);

        return Inertia::render('DevAdmin/SystemConfig/SoftwareMenu/Index', [
            'items' => $menus,
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function create()
    {
        $parents = SoftwareMenu::whereNull('parent_id')->get();
        return Inertia::render('DevAdmin/SystemConfig/SoftwareMenu/Form', [
            'parents' => $parents,
            'isEdit' => false
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'route'     => 'nullable|string|max:255',
            'icon'      => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:software_menus,id',
            'order'     => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        SoftwareMenu::create($request->all());
        $this->menuService->clearCache(Auth::guard('admin')->user());

        return redirect()->route('devAdmin.systemConfig.software.menu.index')->with('success', 'Software Menu created successfully.');
    }

    public function edit(SoftwareMenu $menu)
    {
        $parents = SoftwareMenu::whereNull('parent_id')->where('id', '!=', $menu->id)->get();
        return Inertia::render('DevAdmin/SystemConfig/SoftwareMenu/Form', [
            'menu' => $menu,
            'parents' => $parents,
            'isEdit' => true
        ]);
    }

    public function update(Request $request, SoftwareMenu $menu)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'route'     => 'nullable|string|max:255',
            'icon'      => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:software_menus,id',
            'order'     => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $menu->update($request->all());
        $this->menuService->clearCache(Auth::guard('admin')->user());

        return redirect()->route('devAdmin.systemConfig.software.menu.index')->with('success', 'Software Menu updated successfully.');
    }

    public function destroy(SoftwareMenu $menu)
    {
        $menu->delete();
        $this->menuService->clearCache(Auth::guard('admin')->user());

        return redirect()->back()->with('success', 'Software Menu deleted successfully.');
    }
}
