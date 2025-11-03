<?php

namespace App\Http\Controllers\DevAdmin;

use App\Http\Controllers\Controller;
use App\Models\SoftwareMenu;
use App\Services\MenuService;
use Illuminate\Http\Request;

class SoftwareMenuController extends Controller
{
    protected MenuService $menuService;

    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

public function index(Request $request)
{
    $search = $request->get('search');
    $sort = $request->get('sort', 'created_at');
    $direction = $request->get('direction', 'desc');

    $menus = SoftwareMenu::with('childrenRecursive')
        ->when($search, function ($q) use ($search) {
            $q->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('route', 'like', "%{$search}%");
            });
        })
        ->whereNull('parent_id')
        ->orderBy('order')
        ->orderBy($sort, $direction)
        ->paginate(10);

    return view('devAdmin.systemConfig.softwareMenu.index', compact('menus', 'search', 'sort', 'direction'));
}


    public function create()
    {
        $parents = SoftwareMenu::whereNull('parent_id')->get();
        return view('devAdmin.systemConfig.softwareMenu.create', compact('parents'));
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
        $this->menuService->clearAllCache();

        return redirect()->route('devAdmin.systemConfig.software.menu.index')->with('success', 'Menu created successfully.');
    }

    public function edit(SoftwareMenu $menu)
    {
        $parents = SoftwareMenu::whereNull('parent_id')->where('id', '!=', $menu->id)->get();
        return view('devAdmin.systemConfig.softwareMenu.edit', compact('menu', 'parents'));
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
        $this->menuService->clearAllCache();

        return redirect()->route('devAdmin.systemConfig.software.menu.index')->with('success', 'Menu updated successfully.');
    }

    public function destroy(SoftwareMenu $menu)
    {
        $menu->delete();
        $this->menuService->clearAllCache();

        return redirect()->back()->with('success', 'Menu deleted successfully.');
    }
}
