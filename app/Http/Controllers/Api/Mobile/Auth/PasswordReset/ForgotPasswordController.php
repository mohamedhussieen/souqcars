<?php

namespace App\Http\Controllers\Api\Mobile\Auth\PasswordReset;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Auth\PasswordReset\ForgotPasswordRequest;
use App\Services\PasswordResetService;

/** Sends a 6-digit OTP for the forgot-password flow; always responds the same way regardless of email existence. */
class ForgotPasswordController extends BaseApiController
{
    public function __construct(private readonly PasswordResetService $passwordResetService) {}

    public function __invoke(ForgotPasswordRequest $request)
    {
        $this->passwordResetService->sendOtp($request->input('email'));

        return $this->success(null, __('messages.auth.otp_sent'));
    }
}
