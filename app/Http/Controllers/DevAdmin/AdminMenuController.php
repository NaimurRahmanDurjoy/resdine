<?php

namespace App\Http\Controllers\DevAdmin;

use App\Http\Controllers\Controller;
use App\Models\AdminMenu;
use App\Services\DevAdminMenuService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminMenuController extends Controller
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

        $menus = AdminMenu::with('childrenRecursive')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('route', 'like', "%{$search}%");
            })
            ->whereNull('parent_id')
            ->orderBy($sort, $direction)
            ->paginate(10);

        return Inertia::render('DevAdmin/SystemConfig/AdminMenu/Index', [
            'menus' => $menus,
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function create()
    {
        $parents = AdminMenu::whereNull('parent_id')->get();
        return Inertia::render('DevAdmin/SystemConfig/AdminMenu/Form', [
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
            'parent_id' => 'nullable|exists:admin_menus,id',
            'order'     => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        AdminMenu::create($request->all());
        $this->menuService->clearCache(Auth::guard('admin')->user());

        return redirect()->route('devAdmin.systemConfig.adminPanel.menu.index')->with('success', 'Menu created successfully.');
    }

    public function edit(AdminMenu $menu)
    {
        $parents = AdminMenu::whereNull('parent_id')->where('id', '!=', $menu->id)->get();
        return Inertia::render('DevAdmin/SystemConfig/AdminMenu/Form', [
            'menu' => $menu,
            'parents' => $parents,
            'isEdit' => true
        ]);
    }

    public function update(Request $request, AdminMenu $menu)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'route'     => 'nullable|string|max:255',
            'icon'      => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:admin_menus,id',
            'order'     => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $menu->update($request->all());
        $this->menuService->clearCache(Auth::guard('admin')->user());

        return redirect()->route('devAdmin.systemConfig.adminPanel.menu.index')->with('success', 'Menu updated successfully.');
    }

    public function destroy(AdminMenu $menu)
    {
        $menu->delete();
        $this->menuService->clearCache(Auth::guard('admin')->user());

        return redirect()->back()->with('success', 'Menu deleted successfully.');
    }
}
