<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */

    public function handle($request, Closure $next, string $role)
    {
        $user = Auth::guard('admin')->user() ?? Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'You must be logged in.');
        }

        // If role is a string (Admin)
        if (is_string($user->role)) {
            if ($user->role !== $role) {
                abort(403, 'Unauthorized: Requires ' . $role . ' role.');
            }
        }
        // If role is an object/relationship (User)
        else {
            if (!$user->role || $user->role->name !== $role) {
                abort(403, 'Unauthorized: Requires ' . $role . ' role.');
            }
        }

        return $next($request);
    }
}
