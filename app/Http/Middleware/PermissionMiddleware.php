<?php
namespace App\Http\Middleware;

use Closure;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // 1. If not logged in, block
        if (!$user) {
            return redirect()->route('admin.login')->with('error', 'You must be logged in.');
        }

        // 2. Check for route-based permission
        $routeName = $request->route()->getName();
        
        if (!$this->permissionService->hasRoutePermission($user, $routeName)) {
            abort(403, "Access Denied: You do not have permission to access " . ($routeName ?? 'this page'));
        }

        return $next($request);
    }
}
