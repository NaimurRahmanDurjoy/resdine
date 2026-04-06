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
}
