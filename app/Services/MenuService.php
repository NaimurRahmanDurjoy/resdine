<?php

namespace App\Services;

use App\Models\SoftwareMenu;

class MenuService extends BaseMenuService
{
    public function __construct()
    {
        $this->model = SoftwareMenu::class;
        $this->accessRelation = 'users';
        $this->foreignKey = 'user_id';
        $this->cachePrefix = 'admin_menu';
    }
}
