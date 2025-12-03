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
            return ($this->model)::query()
                ->whereHas($this->accessRelation, fn($q) => $q->where($this->foreignKey, $entity->id))
                ->where('is_active', true)
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
    // public function isActive($menu): bool
    // {
    //     if ($menu->route && request()->routeIs($menu->route . '.*')) {
    //         return true;
    //     }

    //     foreach ($menu->children as $child) {
    //         if ($this->isActive($child)) {
    //             return true;
    //         }
    //     }

    //     return false;
    // }
    public function isActive($menu): bool
    {
        if ($menu->route) {
            if (
                request()->routeIs($menu->route) ||
                request()->routeIs($menu->route . '.*')
            ) {
                return true;
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
                'model' => $menu,
                'url' => $this->getUrl($menu),
                'isActive' => $this->isActive($menu),
                'hasChildren' => $menu->children->count() > 0,
                'children' => $this->prepareForView($menu->children)
            ];
        });
    }
}
