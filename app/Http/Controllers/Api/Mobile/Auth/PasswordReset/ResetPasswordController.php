<?php

namespace App\Http\Controllers\Api\Mobile\Auth\PasswordReset;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Auth\PasswordReset\ResetPasswordRequest;
use App\Services\PasswordResetService;

/** Completes the forgot-password flow: verifies the reset token and updates the password. */
class ResetPasswordController extends BaseApiController
{
    public function __construct(private readonly PasswordResetService $passwordResetService) {}

    public function __invoke(ResetPasswordRequest $request)
    {
        $this->passwordResetService->resetPassword(
            $request->input('email'),
            $request->input('reset_token'),
            $request->input('password')
        );

        return $this->success(null, __('messages.auth.password_reset'));
    }
}
