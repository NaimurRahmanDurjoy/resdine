<?php

namespace App\Http\Controllers\DevAdmin;

use App\Http\Controllers\Controller;
use App\Models\AdminMenu;
use App\Services\DevAdminMenuService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminInternalLinkController extends Controller
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

        return Inertia::render('DevAdmin/SystemConfig/AdminInternalLink/Index', [
            'items' => $menus,
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }
}
