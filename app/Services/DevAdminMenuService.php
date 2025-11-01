<?php
namespace App\Services;

use App\Models\AdminMenu;

class DevAdminMenuService extends BaseMenuService
{
    public function __construct()
    {
        $this->model = AdminMenu::class;
        $this->accessRelation = 'access';
        $this->foreignKey = 'admin_id'; // admin_menu_access.admin_id (developer admin)
        $this->cachePrefix = 'devadmin_menu';
    }
}
