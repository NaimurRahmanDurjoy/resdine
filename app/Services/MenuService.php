<?php
namespace App\Services;

use App\Models\SoftwareMenu;

class MenuService extends BaseMenuService
{
    public function __construct()
    {
        $this->model = SoftwareMenu::class;
        $this->cachePrefix = 'admin_menu_v3';
        $this->shouldFilterByActions = true;
    }
}
