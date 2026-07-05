<?php

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\AdminAuthService;
use Illuminate\Http\Request;

/** Revokes the current Sanctum token to log out the authenticated admin. */
class AdminLogoutController extends BaseApiController
{
    public function __construct(private readonly AdminAuthService $adminAuthService) {}

    /** Deletes the current access token and returns a success response. */
    public function __invoke(Request $request)
    {
        $this->adminAuthService->logout($request->user());

        return $this->success(null, __('messages.auth.logout_success'));
    }
}
