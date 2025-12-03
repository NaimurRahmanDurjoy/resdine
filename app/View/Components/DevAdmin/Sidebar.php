<?php
namespace App\View\Components\DevAdmin;

use Illuminate\View\Component;
use App\Services\DevAdminMenuService;
use Illuminate\Support\Facades\Auth;

class Sidebar extends Component
{
    public $menus;
    public $sidebarOpen;

    public function __construct(DevAdminMenuService $menuService, $sidebarOpen = false)
    {
        // Simplified guard check
        $admin = Auth::guard('admin')->user();
        $this->menus = $admin ? $menuService->prepareForView($menuService->getMenusFor($admin)) : collect();
        $this->sidebarOpen = $sidebarOpen;
    }

    public function render()
    {
        return view('components.dev-admin.sidebar');
    }
}