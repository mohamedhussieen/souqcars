<?php

namespace App\Http\Controllers\Api\Mobile\Auth\PasswordReset;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Auth\PasswordReset\VerifyResetOtpRequest;
use App\Services\PasswordResetService;

/** Verifies the forgot-password OTP and issues a short-lived reset token. */
class VerifyResetOtpController extends BaseApiController
{
    public function __construct(private readonly PasswordResetService $passwordResetService) {}

    public function __invoke(VerifyResetOtpRequest $request)
    {
        $resetToken = $this->passwordResetService->verifyOtp(
            $request->input('email'),
            $request->input('otp')
        );

        return $this->success(['reset_token' => $resetToken], __('messages.auth.otp_verified'));
    }
}
