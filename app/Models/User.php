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

    // Redirect user based on role
    public function redirectToDashboard(): string
    {
        $roleName = $this->role->name ?? null;

        return match ($roleName) {
            'admin' => route('admin.dashboard'),
            'cashier' => route('cashier.dashboard'),
            'customer' => route('home'),
            default => route('home'),
        };
    }

    // Relationship to user-specific permissions
    public function userPermissions()
    {
        return $this->hasMany(UserPermission::class);
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
