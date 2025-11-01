<?php

namespace App\View\Components\DevAdmin;

use Illuminate\View\Component;
use App\Services\DevAdminMenuService;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

class Sidebar extends Component
{
    public $menus;

    public function __construct(DevAdminMenuService $menuService)
    {
        // Use the correct guard
         $admin = Auth::guard('admin')->check() ? Auth::guard('admin')->user() : Auth::user(); // or 'devAdmin' if you defined it
        $this->menus = $admin ? $menuService->getMenusFor($admin) : collect();
    }

    public function render()
    {
        return view('components.dev-admin.sidebar');
    }
}
