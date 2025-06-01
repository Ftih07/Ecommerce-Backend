<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // If user is not authenticated, deny access
        // return response()->json(['message' => $roles]);
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Split roles by comma and trim whitespace
        // $roleNames = array_map('trim', explode(',', $roles));

        // Check if user has any of the required roles
        foreach ($roles as $roleName) {
            if ($request->user()->hasRole($roleName)) {
                return $next($request);
            }
        }

        // If user doesn't have any of the required roles, deny access
        return response()->json(['message' => 'Forbidden. You need one of these roles: ' . $roles], 403);
    }
}
