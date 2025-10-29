<?php

namespace App\Services;

use App\Models\AdminMenu;

class DevMenuService extends BaseMenuService
{
    public function __construct()
    {
        $this->model = AdminMenu::class;
        $this->accessRelation = 'access';
        $this->foreignKey = 'admin_id';
        $this->cachePrefix = 'dev_admin_menu';
    }
}
