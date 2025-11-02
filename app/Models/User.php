<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role_id'];

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

    // Redirect user based on role
    public function redirectToDashboard(): string
    {
        $roleName = $this->role->name ?? null;

        return match($roleName) {
            'admin' => route('admin.dashboard'),
            'cashier' => route('cashier.dashboard'),
            'customer' => route('home'),
            default => route('home'),
        };
    }

    // Get menus the user has access to
    public function softwareMenus()
    {
        return $this->belongsToMany(SoftwareMenu::class, 'user_id');
    }

    public function accessibleMenus()
    {
        return app(\App\Services\MenuService::class)->getMenusFor($this);
    }


}
