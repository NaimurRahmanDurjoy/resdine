<?php

namespace App\Http\Controllers\DevAdmin;

use App\Http\Controllers\Controller;
use App\Models\AdminMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminMenuSortingController extends Controller
{
    public function index()
    {
        $menus = Adminmenu::with('childrenRecursive')->whereNull('parent_id')->orderBy('order')->get();
        return view('devAdmin.systemConfig.adminMenu.menuSorting.index', compact('menus'));
    }

public function updateOrder(Request $request)
    {$request->validate([
        'structure' => 'required|array',
    ]);

    DB::transaction(function () use ($request) {
        $this->updateMenuHierarchy($request->input('structure'), null);
    });

    return response()->json(['success' => true]);
}


private function updateMenuHierarchy($items, $parentId)
{
    foreach ($items as $index => $item) {
        Adminmenu::where('id', $item['id'])->update([
            'order' => $index + 1,
            'parent_id' => $parentId,
        ]);

        if (!empty($item['children'])) {
            $this->updateMenuHierarchy($item['children'], $item['id']);
        }
    }
}

}