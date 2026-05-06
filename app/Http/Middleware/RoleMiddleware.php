<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Unauthorized.');
        }

        $userRoles = $user->roles()
            ->pluck('roles.name')
            ->map(function ($role) {
                return strtolower(str_replace(' ', '_', $role));
            })
            ->toArray();

        // Super Admin can access everything
        if (in_array('super_admin', $userRoles)) {
            return $next($request);
        }

        foreach ($roles as $role) {
            $role = strtolower(str_replace(' ', '_', $role));

            if (in_array($role, $userRoles)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized.');
    }
}