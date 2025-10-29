<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

abstract class BaseMenuService
{
    /**
     * Model class to be used (e.g., AdminMenu::class or SoftwareMenu::class)
     */
    protected string $model;

    /**
     * Relation name to check access (e.g., 'access', 'users')
     */
    protected string $accessRelation;

    /**
     * Foreign key column on pivot table (e.g., 'admin_id', 'user_id')
     */
    protected string $foreignKey;

    /**
     * Cache prefix (used to separate different user types)
     */
    protected string $cachePrefix = 'menu';

    /**
     * Fetch all menus for a specific user/admin.
     */
    public function getMenusFor(Model $entity)
    {
        $cacheKey = "{$this->cachePrefix}_{$entity->id}";

        return Cache::remember($cacheKey, now()->addHours(1), function () use ($entity) {
            $query = ($this->model)::query()
                ->whereHas($this->accessRelation, fn($q) => $q->where($this->foreignKey, $entity->id))
                ->where('is_active', true)
                ->whereNull('parent_id')
                ->orderBy('order')
                ->with('childrenRecursive');

            return $query->get();
        });
    }

    /**
     * Clear cache for this user/admin.
     */
    public function clearCache(Model $entity)
    {
        Cache::forget("{$this->cachePrefix}_{$entity->id}");
    }
}
