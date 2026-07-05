<?php

namespace App\Http\Controllers\Api\Mobile\Auth;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;

/** Handles new user registration for the mobile API. */
class RegisterController extends BaseApiController
{
    public function __construct(private readonly AuthService $authService) {}

    /** Registers a new user and returns their profile with a Sanctum token. */
    public function __invoke(RegisterRequest $request)
    {
        $result = $this->authService->register($request->validated());

        return $this->success(
            [
                'user'  => new UserResource($result['user']),
                'token' => $result['token'],
            ],
            __('messages.auth.registered'),
            201
        );
    }
}
