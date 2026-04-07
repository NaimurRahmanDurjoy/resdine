<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoftwareMenuAction extends Model
{
    /**
     * Centralized list of valid actions for standard resources.
     */
    public const ACTIONS = ['view', 'create', 'edit', 'delete'];

    /**
     * Map action names to Laravel route suffixes.
     */
    public const ROUTE_MAP = [
        'view'   => 'index',
        'create' => 'create',
        'edit'   => 'edit',
        'delete' => 'destroy',
        'store'  => 'store',
        'update' => 'update',
    ];

    protected $fillable = [
        'software_menu_id',
        'action',
        'route',
        'is_active',
    ];

    /**
     * Generate a route name based on base route and action.
     */
    public static function generateRouteName(string $baseRoute, string $action): string
    {
        $suffix = self::ROUTE_MAP[$action] ?? $action;
        return "{$baseRoute}.{$suffix}";
    }

    /**
     * Get the base permission action for a given route action.
     * E.g., 'store' maps back to 'create' permission.
     */
    public static function getBaseAction(string $routeAction): string
    {
        return match ($routeAction) {
            'store'   => 'create',
            'update'  => 'edit',
            'destroy' => 'delete',
            'index'   => 'view',
            default    => $routeAction,
        };
    }

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
