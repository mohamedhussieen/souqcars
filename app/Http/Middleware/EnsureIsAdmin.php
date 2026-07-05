<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Restricts access to authenticated users holding the admin role. */
class EnsureIsAdmin
{
    use ApiResponseTrait;

    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()?->hasRole(UserRole::Admin->value)) {
            return $this->unauthorized(__('messages.admin.forbidden'));
        }

        return $next($request);
    }
}
