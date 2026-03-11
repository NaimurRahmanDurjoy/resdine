<?php

namespace App\Http\Controllers\DevAdmin;

use App\Http\Controllers\Controller;
use App\Models\AdminMenu;
use App\Services\DevAdminMenuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminMenuSortingController extends Controller
{
    protected DevAdminMenuService $menuService;

    public function __construct(DevAdminMenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    public function index()
    {
        $menus = AdminMenu::with('childrenRecursive')->whereNull('parent_id')->orderBy('order')->get();
        return Inertia::render('DevAdmin/SystemConfig/AdminMenu/MenuSorting', [
            'items' => $menus,
        ]);
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'structure' => 'required|array',
        ]);

        DB::transaction(function () use ($request) {
            $this->updateMenuHierarchy($request->input('structure'), null);
        });

        $this->menuService->clearCache(Auth::guard('admin')->user());

        return back()->with('success', 'Menu order updated successfully');
    }


    private function updateMenuHierarchy($items, $parentId)
    {
        foreach ($items as $index => $item) {
            AdminMenu::where('id', $item['id'])->update([
                'order' => $index + 1,
                'parent_id' => $parentId,
            ]);

            if (!empty($item['children'])) {
                $this->updateMenuHierarchy($item['children'], $item['id']);
            }
        }
    }
}
