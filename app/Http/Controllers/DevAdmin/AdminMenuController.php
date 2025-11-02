<?php

namespace App\Http\Controllers\DevAdmin;

use App\Http\Controllers\Controller;
use App\Models\AdminMenu;
use App\Services\DevAdminMenuService;
use Illuminate\Http\Request;

class AdminMenuController extends Controller
{
    protected DevAdminMenuService $menuService;

    public function __construct(DevAdminMenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    public function index()
    {
        $menus = AdminMenu::with('children')->whereNull('parent_id')->orderBy('order')->get();
        return view('devAdmin.systemConfig.admin-menu.index', compact('menus'));
    }

    public function create()
    {
        $parents = AdminMenu::whereNull('parent_id')->get();
        return view('devAdmin.systemConfig.admin-menu.create', compact('parents'));
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
        $this->menuService->clearAllCache(); // optional method to clear all cached menus

        return redirect()->route('systemConfig.adminPanel.menu.index')->with('success', 'Menu created successfully.');
    }

    public function edit(AdminMenu $menu)
    {
        $parents = AdminMenu::whereNull('parent_id')->where('id', '!=', $menu->id)->get();
        return view('devAdmin.systemConfig.admin-menu.edit', compact('menu', 'parents'));
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
        $this->menuService->clearAllCache();

        return redirect()->route('systemConfig.adminPanel.menu.index')->with('success', 'Menu updated successfully.');
    }

    public function destroy(AdminMenu $menu)
    {
        $menu->delete();
        $this->menuService->clearAllCache();

        return redirect()->back()->with('success', 'Menu deleted successfully.');
    }
}
