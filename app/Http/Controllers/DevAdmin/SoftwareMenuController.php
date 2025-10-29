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

    public function index()
    {
        $menus = SoftwareMenu::with('children')->whereNull('parent_id')->orderBy('order')->get();
        return view('devAdmin.systemConfig.softwareMenu.index', compact('menus'));
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

        return redirect()->route('systemConfig.software.menu.index')->with('success', 'Menu created successfully.');
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

        return redirect()->route('systemConfig.software.menu.index')->with('success', 'Menu updated successfully.');
    }

    public function destroy(SoftwareMenu $menu)
    {
        $menu->delete();
        $this->menuService->clearAllCache();

        return redirect()->back()->with('success', 'Menu deleted successfully.');
    }
}
