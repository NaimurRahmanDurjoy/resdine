<?php

namespace App\Models;

class Role extends BaseModel
{
    protected $fillable = ['name', 'description', 'status'];

    /**
     * Relationship to permissions
     */
    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class);
    }

    /**
     * Relationship to permissions (actions)
     */
    public function permissions()
    {
        return $this->belongsToMany(SoftwareMenuAction::class, 'role_permissions', 'role_id', 'software_menu_action_id')
            ->withPivot('is_allowed')
            ->withTimestamps();
    }
}
