<?php

namespace App\Models;

class SoftwareMenu extends BaseModel
{
    protected $fillable = ['name', 'route', 'icon', 'parent_id', 'order', 'is_active'];

    public function children()
    {
        return $this->hasMany(SoftwareMenu::class, 'parent_id')->orderBy('order');
    }

    // Recursive children
    public function childrenRecursive()
    {
        return $this->children()->with(['actions', 'childrenRecursive']);
    }

    public function access()
    {
        return $this->belongsToMany(User::class, 'software_menu_access' , 'menu_id', 'user_id');
    }

    public function actions()
    {
        return $this->hasMany(SoftwareMenuAction::class, 'software_menu_id', 'id');
    }


}
