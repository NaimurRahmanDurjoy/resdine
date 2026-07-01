<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use App\Models\UserProfile;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role_id', 'phone', 'branch_id', 'status'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationship to Role
    public function role()
    {
        return $this->belongsTo(Role::class)->withDefault([
            'name' => 'guest',
        ]);
    }
    // Relationship to Branch
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Resolve active branch ID based on role and session
    public function getActiveBranchId()
    {
        $roleName = strtolower($this->role->name ?? '');
        if ($roleName === 'admin') {
            return session('active_branch_id') ?: ($this->branch_id ?: \App\Models\Branch::first()?->id);
        }
        
        return $this->branch_id ?: \App\Models\Branch::first()?->id;
    }

    /**
     * Redirect to the user's first RBAC-permitted menu route, or home.
     */
    public function redirectToDashboard(): string
    {
        $routeName = $this->role?->landingMenu?->route;

        return $routeName && \Illuminate\Support\Facades\Route::has($routeName)
            ? route($routeName)
            : route('home');
    }

    // Relationship to user-specific permissions
    public function userPermissions()
    {
        return $this->hasMany(UserPermission::class);
    }

    /**
     * Relationship to permissions overrides (actions)
     */
    public function permissions()
    {
        return $this->belongsToMany(SoftwareMenuAction::class, 'user_permissions', 'user_id', 'software_menu_action_id')
            ->withPivot('is_allowed')
            ->withTimestamps();
    }

    // Get menus the user has access to
    public function softwareMenus()
    {
        // This was the old way, keeping for compatibility if needed, 
        // but now we use Role + User Permissions.
        return $this->belongsToMany(SoftwareMenu::class, 'software_menu_access', 'user_id', 'menu_id');
    }

    public function accessibleMenus()
    {
        return app(\App\Services\MenuService::class)->getMenusFor($this);
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }
}
