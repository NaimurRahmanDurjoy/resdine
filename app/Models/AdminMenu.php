<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminMenu extends Model
{
    protected $fillable = ['name', 'route', 'icon', 'parent_id', 'order', 'is_active'];

    public function children()
    {
        return $this->hasMany(AdminMenu::class, 'parent_id')->where('is_active', 1)->orderBy('order');
    }

    // Recursive children
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function parent()
    {
        return $this->belongsTo(AdminMenu::class, 'parent_id');
    }

    public function access()
    {
        return $this->belongsToMany(Admin::class, 'admin_menu_access', 'menu_id', 'admin_id');
    }
    
}
