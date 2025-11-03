<?php

namespace App\Http\Controllers\DevAdmin;

use App\Http\Controllers\Controller;
use App\Models\SoftwareMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SoftwareMenuSortingController extends Controller
{
    public function index()
    {
        $menus = SoftwareMenu::with('childrenRecursive')->whereNull('parent_id')->orderBy('order')->get();
        return view('devAdmin.systemConfig.softwareMenu.menuSorting.index', compact('menus'));
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
        SoftwareMenu::where('id', $item['id'])->update([
            'order' => $index + 1,
            'parent_id' => $parentId,
        ]);

        if (!empty($item['children'])) {
            $this->updateMenuHierarchy($item['children'], $item['id']);
        }
    }
}

}