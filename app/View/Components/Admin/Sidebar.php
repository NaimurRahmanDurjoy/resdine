<?php
namespace App\View\Components\Admin;

use Illuminate\View\Component;
use App\Services\MenuService;
use Illuminate\Support\Facades\Auth;

class Sidebar extends Component
{
    public $menus;
    public $sidebarOpen;

    public function __construct(MenuService $menuService, $sidebarOpen = false)
    {
        $user = Auth::user();
        // Get menus and prepare them for view
        $this->menus = $user ? $menuService->prepareForView($menuService->getMenusFor($user)) : collect();
        $this->sidebarOpen = $sidebarOpen;
    }

    public function render()
    {
        return view('components.admin.sidebar');
    }
}
