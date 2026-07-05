<?php

namespace App\Http\Controllers\Api\Mobile\Auth;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\AuthService;
use Illuminate\Http\Request;

/** Revokes the current Sanctum token to log out the authenticated user. */
class LogoutController extends BaseApiController
{
    public function __construct(private readonly AuthService $authService) {}

    /** Deletes the current access token and returns a success response. */
    public function __invoke(Request $request)
    {
        $this->authService->logout($request->user());

        return $this->success(null, __('messages.auth.logout_success'));
    }
}
