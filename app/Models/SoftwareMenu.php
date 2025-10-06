<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoftwareMenu extends Model
{
    protected $fillable = ['name', 'route', 'icon', 'parent_id', 'order', 'is_active'];

    public function children()
    {
        return $this->hasMany(SoftwareMenu::class, 'parent_id')->orderBy('order');
    }

    // Recursive children
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'software_menu_access', 'menu_id', 'user_id');
    }
}
