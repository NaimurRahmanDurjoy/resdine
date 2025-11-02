<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, string $role)
    {
        // Determine which guard to use based on the role
        $guard = $role === 'developer' ? 'admin' : 'web';
        $user = Auth::guard($guard)->user();

        // If not logged in, redirect to the appropriate login
        if (!$user) {
            return match ($role) {
                'developer' => redirect()->route('devAdmin.login')->with('error', 'You must be logged in.'),
                'admin' => redirect()->route('admin.login')->with('error', 'You must be logged in.'),
                default => redirect()->route('login')->with('error', 'You must be logged in.'),
            };
        }

        // ---- ROLE CHECK ----
        if ($guard === 'web') {
            // For "users" table (role_id + relation)
            if (!$user->role || strtolower($user->role->name) !== strtolower($role)) {
                abort(403, "Unauthorized: Requires {$role} role.");
            }
        } else {
            // For "admins" table (direct role field)
            if (strtolower($user->role) !== strtolower($role)) {
                abort(403, "Unauthorized: Requires {$role} role.");
            }
        }

        return $next($request);
    }
}
