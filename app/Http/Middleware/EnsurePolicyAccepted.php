<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Blocks access to protected endpoints until the authenticated user accepts the app policy. */
class EnsurePolicyAccepted
{
    use ApiResponseTrait;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && !$user->hasAcceptedPolicy()) {
            return $this->error(__('messages.auth.policy_not_accepted'), 403);
        }

        return $next($request);
    }
}
