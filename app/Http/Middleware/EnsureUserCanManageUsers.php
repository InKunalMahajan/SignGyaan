<?php

namespace App\Http\Middleware;

use App\Services\RolePermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserCanManageUsers
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless(
            $user && app(RolePermissionService::class)->allows($user, RolePermissionService::PERMISSION_MANAGE_USERS),
            403,
            'User management access is required.'
        );

        return $next($request);
    }
}
