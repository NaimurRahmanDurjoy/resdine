<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
 
    protected $fillable = ['name', 'email', 'password', 'role_id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ['password', 'remember_token'];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
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

    // Redirect based on role
public function redirectToDashboard()
{
    $roleName = $this->role->name ?? null;

    if ($roleName === 'admin') return route('admin.dashboard');
    if ($roleName === 'cashier') return route('cashier.dashboard');
    if ($roleName === 'customer') return route('home');

    return route('login');
}


}
