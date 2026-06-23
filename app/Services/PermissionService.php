<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;

class PermissionService
{
    /**
     * Get all allowed action IDs for the user
     */
    public function getAllowedActionIds($user): array
    {
        return Cache::remember("perm_{$user->id}", 3600, function () use ($user) {

            // If user has a role, get the role's allowed permissions
            $roleIds = DB::table('role_permissions')
                ->where('role_id', $user->role_id)
                ->where('is_allowed', true)
                ->pluck('software_menu_action_id')
                ->toArray();

            // Get user specific permissions (overrides)
            $userPerms = DB::table('user_permissions')
                ->where('user_id', $user->id)
                ->get();

            $allowOverrides = $userPerms->where('is_allowed', true)
                ->pluck('software_menu_action_id')
                ->toArray();

            $denyOverrides = $userPerms->where('is_allowed', false)
                ->pluck('software_menu_action_id')
                ->toArray();

            // Permissions = (Role + Allowed Overrides) - Denied Overrides
            $totalAllowed = array_unique(array_merge($roleIds, $allowOverrides));
            return array_values(array_diff($totalAllowed, $denyOverrides));
        });
    }

    /**
     * Check if a user has permission for a specific route
     */
    public function hasRoutePermission($user, string $routeName): bool
    {
        // 1. Find if this route is defined as an action
        $action = \App\Models\SoftwareMenuAction::where('route', $routeName)->first();

        // 2. If it's not found exactly, try mapping it to a base permission (e.g., .store -> .create)
        if (!$action) {
            $segments = explode('.', $routeName);
            if (count($segments) >= 2) {
                $lastSegment = array_pop($segments);
                $baseAction = \App\Models\SoftwareMenuAction::getBaseAction($lastSegment);
                
                // Reconstruct the route name with the base action suffix (e.g., .index, .create)
                // Note: getBaseAction maps index->view, destroy->delete, etc.
                $baseActionSuffix = \App\Models\SoftwareMenuAction::ROUTE_MAP[$baseAction] ?? $baseAction;
                $baseRouteName = implode('.', $segments) . '.' . $baseActionSuffix;
                
                $action = \App\Models\SoftwareMenuAction::where('route', $baseRouteName)->first();
            }
        }

        // 3. If still no action found, it's either an unrestricted route or handled globally
        if (!$action) {
            return true; 
        }

        // 4. Check against allowed action IDs for the user
        $allowedIds = $this->getAllowedActionIds($user);

        return in_array($action->id, $allowedIds);
    }

    /**
     * Update user specific permissions (overrides)
     */
    public function updateUserPermissions($user, array $overrides): void
    {
        DB::transaction(function () use ($user, $overrides) {
            // Deduplicate overrides by action_id
            $syncData = [];
            foreach ($overrides as $override) {
                $actionId = $override['action_id'] ?? $override['id'] ?? null;
                if ($actionId && $override['is_allowed'] !== null) {
                    $syncData[$actionId] = ['is_allowed' => $override['is_allowed']];
                }
            }

            // Delta Sync: Only add/update/delete what's necessary
            $user->permissions()->sync($syncData);
        });

        // Invalidate cache
        Cache::forget("perm_{$user->id}");
        if (class_exists(\App\Services\MenuService::class)) {
            app(\App\Services\MenuService::class)->clearCache($user);
        }
    }

    /**
    * Get structured permission tree with grouped actions
    */
    public function getPermissionTree()
    {
        return \App\Models\SoftwareMenu::where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('order')
            ->with(['actions', 'childrenRecursive'])
            ->get()
            ->map(function ($menu) {
                return $this->processMenuNode($menu);
            });
    }

    /**
     * Recursively process menu nodes to group actions
     */
    protected function processMenuNode($menu)
    {
        // Recursively process children first
        if ($menu->childrenRecursive) {
            $menu->childrenRecursive->each(function ($child) {
                $this->processMenuNode($child);
            });
        }

        // Group regular actions
        $standardActions = \App\Models\SoftwareMenuAction::ACTIONS;
        $grouped = [
            'view'   => null,
            'create' => null,
            'edit'   => null,
            'delete' => null,
            'others' => []
        ];

        foreach ($menu->actions as $action) {
            $actionLabel = strtolower($action->action);
            if (in_array($actionLabel, $standardActions)) {
                $grouped[$actionLabel] = $action;
            } else {
                $grouped['others'][] = $action;
            }
        }

        $menu->grouped_actions = $grouped;
        
        return $menu;
    }

    /**
     * Update role permissions
     */
    public function updateRolePermissions($role, array $actionIds): void
    {
        DB::transaction(function () use ($role, $actionIds) {
            // Deduplicate and format for sync
            $actionIds = array_unique(array_map('intval', $actionIds));
            
            $syncData = [];
            foreach ($actionIds as $id) {
                $syncData[$id] = ['is_allowed' => true];
            }

            // Delta Sync: Only adds/removes differences
            $role->permissions()->sync($syncData);
        });

        // Invalidate cache for all users of this role
        \App\Models\User::where('role_id', $role->id)->chunk(100, function ($users) {
            foreach ($users as $user) {
                Cache::forget("perm_{$user->id}");
            }
        });
    }
}
