<?php

namespace App\Models;

class UserPermission extends BaseModel
{
    protected $fillable = [
        'user_id',
        'software_menu_action_id',
        'is_allowed',
    ];

    protected $casts = [
        'is_allowed' => 'boolean',
    ];

    /**
     * Relationship to the User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship to the SoftwareMenuAction
     */
    public function action()
    {
        return $this->belongsTo(SoftwareMenuAction::class, 'software_menu_action_id');
    }

    /**
     * Scope to filter by permission status
     */
    public function scopeAllowed($query)
    {
        return $query->where('is_allowed', true);
    }

    /**
     * Scope to filter by denial
     */
    public function scopeDenied($query)
    {
        return $query->where('is_allowed', false);
    }
}
