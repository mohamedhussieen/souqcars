<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Authenticates the request via the 'sanctum' guard if a valid Bearer token is present,
 * but never rejects the request when it's absent or invalid — for guest-allowed
 * endpoints (home, car listing/detail, search, showrooms) that still need to know
 * the current user when one is logged in (e.g. to resolve is_favorited).
 */
class OptionalSanctumAuth
{
    /** Resolves and sets the authenticated user on the default guard if the sanctum guard finds one. */
    public function handle(Request $request, Closure $next)
    {
        if ($user = Auth::guard('sanctum')->user()) {
            Auth::setUser($user);
        }

        return $next($request);
    }
}
