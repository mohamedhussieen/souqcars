<?php

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Http\Resources\UserResource;
use App\Services\AdminAuthService;

/** Handles admin dashboard login and token issuance. */
class AdminLoginController extends BaseApiController
{
    public function __construct(private readonly AdminAuthService $adminAuthService) {}

    /** Authenticates the admin and returns their profile with a fresh Sanctum token. */
    public function __invoke(AdminLoginRequest $request)
    {
        $result = $this->adminAuthService->login(
            $request->input('email'),
            $request->input('password')
        );

        if (!$result) {
            return $this->unauthorized(__('messages.auth.login_failed'));
        }

        if (isset($result['inactive'])) {
            return $this->error(__('messages.auth.account_inactive'), 403);
        }

        return $this->success(
            [
                'user'  => new UserResource($result['user']),
                'token' => $result['token'],
            ],
            __('messages.auth.login_success')
        );
    }
}
