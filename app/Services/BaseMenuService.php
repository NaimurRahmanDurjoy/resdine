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
    protected bool $shouldFilterByActions = false;

    public function getMenusFor(Model $entity): Collection
    {
        $cacheKey = "{$this->cachePrefix}_{$entity->id}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($entity) {
            $query = ($this->model)::query();

            // Apply legacy access check for panels not using action-based permissions
            if (!$this->shouldFilterByActions) {
                $query->whereHas($this->accessRelation, fn($q) => $q->where($this->foreignKey, $entity->id));
            }

            $query->where('is_active', true)
                ->whereNull('parent_id')
                ->orderBy('order');

            // Conditionally eager load actions only if needed
            if ($this->shouldFilterByActions) {
                $query->with(['actions', 'childrenRecursive']);
            } else {
                $query->with('childrenRecursive');
            }

            return $query->get();
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

        if ($menu->childrenRecursive) {
            foreach ($menu->childrenRecursive as $child) {
                if ($this->isActive($child)) {
                    return true;
                }
            }
        }    

        return false;
    }


    /**
     * Prepare menu data for view with conditional permission filtering
     */
    public function prepareForView($menus, Model $entity)
    {
        $allowedActionIds = [];
        if ($this->shouldFilterByActions) {
            $permissionService = app(\App\Services\PermissionService::class);
            $allowedActionIds = $permissionService->getAllowedActionIds($entity);
        }

        return $this->recursivePrepare($menus, $entity, $allowedActionIds);
    }

    /**
     * Recursive preparation and filtering logic
     */
    protected function recursivePrepare($menus, $entity, $allowedActionIds)
    {
        return $menus->map(function ($menu) use ($entity, $allowedActionIds) {
            // Recursive call for children
            $children = $this->recursivePrepare($menu->childrenRecursive ?? collect(), $entity, $allowedActionIds);
            
            // Visibility Logic:
            
            if ($this->shouldFilterByActions) {
                // 1. If it's the Software Admin panel using granular Actions
                $hasAllowedActions = $menu->actions->some(fn($a) => in_array($a->id, $allowedActionIds));
                $hasVisibleChildren = $children->count() > 0;

                // Hide if no allowed actions AND no visible children
                if (!$hasAllowedActions && !$hasVisibleChildren) {
                    return null;
                }
            } else {
                // 2. If it's the Developer Panel (DevAdmin) or simple panel
                // $hasVisibleChildren = $children->count() > 0;
                
                // // Hide if no route AND no visible children (folders must have children)
                // if (!$menu->route && !$hasVisibleChildren) {
                //     return null;
                // }
                
                // For DevAdmin, we show all active menus regardless of route or children, so no additional hiding logic is needed here.
            }

            return [
                'id' => $menu->id,
                'name' => $menu->name,
                'route' => $menu->route,
                'icon' => $menu->icon,
                'url' => $this->getUrl($menu),
                'isActive' => $this->isActive($menu),
                'hasChildren' => $children->count() > 0,
                'children' => $children,
                'model' => $menu,
            ];
        })->filter()->values();
    }
}
