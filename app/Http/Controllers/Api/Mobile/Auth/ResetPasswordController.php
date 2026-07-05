<?php

namespace App\Http\Controllers\Api\Mobile\Auth;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\AuthService;

/** Processes a password reset using the provided OTP code and new password. */
class ResetPasswordController extends BaseApiController
{
    public function __construct(private readonly AuthService $authService) {}

    /** Resets the password if the OTP is valid; returns 400 if the OTP is invalid or expired. */
    public function __invoke(ResetPasswordRequest $request)
    {
        $reset = $this->authService->resetPassword(
            $request->input('email'),
            $request->input('otp_code'),
            $request->input('password')
        );

        if (!$reset) {
            return $this->error(__('messages.auth.otp_invalid'));
        }

        return $this->success(null, __('messages.auth.password_reset'));
    }
}
