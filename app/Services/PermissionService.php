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

        // 2. If it's not a restricted action, we might allow it depending on policy 
        // For now, if it's not in the actions table, we assume no restriction or handled by role
        if (!$action) {
            return true; 
        }

        // 3. Get allowed action IDs
        $allowedIds = $this->getAllowedActionIds($user);

        return in_array($action->id, $allowedIds);
    }
}
