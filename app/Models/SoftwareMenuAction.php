<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoftwareMenuAction extends Model
{
    protected $fillable = [
        'software_menu_id',
        'action',
        'route',
        'is_active',
    ];

    public function softwareMenu()
    {
        return $this->belongsTo(SoftwareMenu::class, 'software_menu_id', 'id');
    }

    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class, 'software_menu_action_id');
    }

    public function userPermissions()
    {
        return $this->hasMany(UserPermission::class, 'software_menu_action_id');
    }
}
