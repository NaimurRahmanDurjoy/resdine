<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        // 1. Detect which guard is currently active (web, admin, or customer)
        $guard = collect(config('auth.guards'))->keys()->first(fn($g) => Auth::guard($g)->check());
        
        if (!$guard) {
            abort(401); // Standard Laravel behavior: let 'auth' middleware handle redirects
        }

        $user = Auth::guard($guard)->user();

        // 2. Get the role name dynamically
        // Handles both relationship ($user->role->name) and direct column ($user->role)
        $userRole = is_object($user->role) ? strtolower($user->role->name)   : strtolower($user->role);

        // 3. Check if role matches
        $allowedRoles = array_map('strtolower', $roles);

        if (!in_array($userRole, $allowedRoles)) {
            abort(403, "Access denied. Your role ($userRole) is not authorized for this section.");
        }

        return $next($request);
    }
}