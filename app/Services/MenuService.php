<?php
namespace App\Services;

use App\Models\SoftwareMenu;

class MenuService extends BaseMenuService
{
    public function __construct()
    {
        $this->model = SoftwareMenu::class;
        $this->accessRelation = 'access';
        $this->foreignKey = 'user_id'; // software_menu_access.user_id (software admin)
        $this->cachePrefix = 'admin_menu';
    }
}
