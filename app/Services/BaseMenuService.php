<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseMenuService
{
    protected string $model; // e.g. AdminMenu::class or SoftwareMenu::class
    protected string $accessRelation; // 'access'
    protected string $foreignKey; // 'admin_id' or 'user_id'
    protected string $cachePrefix = 'menu';

    public function getMenusFor(Model $entity): Collection
    {
        $cacheKey = "{$this->cachePrefix}_{$entity->id}";

        return Cache::remember($cacheKey, now()->addHours(1), function () use ($entity) {
            $query = ($this->model)::query();

            // If user is admin (role_id = 1), skip the permission check
            $isAdmin = false;
            
            // Check for role_id == 1 (common for standard admin)
            if (isset($entity->role_id) && $entity->role_id == 1) {
                $isAdmin = true;
            } 
            
            // Check for 'role' attribute or relation
            if (!$isAdmin) {
                $role = $entity->role;
                if ($role) {
                    if (is_string($role)) {
                        $isAdmin = in_array($role, ['admin', 'superadmin', 'developer']);
                    } else {
                        $isAdmin = ($role->name ?? '') === 'admin';
                    }
                }
            }

            if (!$isAdmin) {
                $query->whereHas($this->accessRelation, fn($q) => $q->where($this->foreignKey, $entity->id));
            }

            return $query->where('is_active', true)
                ->whereNull('parent_id')
                ->orderBy('order')
                ->with('childrenRecursive')
                ->get();
        });
    }

    public function clearCache(Model $entity): void
    {
        Cache::forget("{$this->cachePrefix}_{$entity->id}");
    }

    /**
     * Common URL logic for all menu types
     */
    public function getUrl($menu): string
    {
        return $menu->route ? route($menu->route) : '#';
    }

    /**
     * Common active state logic for all menu types
     */

    public function isActive($menu): bool
    {
        if ($menu->route) {
            if (request()->routeIs($menu->route) || request()->routeIs($menu->route . '.*')) {
                return true;
            }

            // If the route name ends in .index, we also check its prefix for related routes
            if (str_ends_with($menu->route, '.index')) {
                $prefix = substr($menu->route, 0, -6);
                if (request()->routeIs($prefix . '.*')) {
                    return true;
                }
            }
        }

        foreach ($menu->children as $child) {
            if ($this->isActive($child)) {
                return true;
            }
        }    

        return false;
    }


    /**
     * Prepare menu data for view (common for all menu types)
     */
    public function prepareForView($menus)
    {
        return $menus->map(function ($menu) {
            return [
                'id' => $menu->id,
                'name' => $menu->name,
                'route' => $menu->route,
                'icon' => $menu->icon,
                'url' => $this->getUrl($menu),
                'isActive' => $this->isActive($menu),
                'hasChildren' => $menu->children->count() > 0,
                'children' => $this->prepareForView($menu->children),
                'model' => $menu,
            ];
        });
    }
}
