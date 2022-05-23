<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;

use Closure;

class CheckPermission
{
    public function handle($request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user->hasAnyRole($roles)) {
            throw new AuthorizationException('You do not have permission to view this page');
        }

        return $next($request);
    }
}
