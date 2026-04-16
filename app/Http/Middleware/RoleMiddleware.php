<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Handles both relationship ($user->role->name) and direct column ($user->role)
        $userRole = is_object($user->role) ? strtolower($user->role->name) : strtolower($user->role);

        // Check if role matches
        $allowedRoles = array_map('strtolower', $roles);

        if (!in_array($userRole, $allowedRoles)) {
            return response()->json([
                'message' => "Access denied. Your role ({$userRole}) is not authorized for this section."
            ], 403);
        }

        return $next($request);
    }
}