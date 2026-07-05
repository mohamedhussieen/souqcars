<?php

namespace App\Http\Controllers\Api\Mobile\Auth;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;

/** Handles user login and token issuance for the mobile API. */
class LoginController extends BaseApiController
{
    public function __construct(private readonly AuthService $authService) {}

    /** Authenticates the user and returns their profile with a fresh Sanctum token. */
    public function __invoke(LoginRequest $request)
    {
        $result = $this->authService->login(
            $request->input('email'),
            $request->input('password'),
            $request->input('fcm_token')
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
