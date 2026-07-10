<?php

namespace App\Http\Controllers\Api\Mobile\Auth;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Services\AuthService;

/** Sends an OTP code to the given email address for password reset. */
class ForgotPasswordController extends BaseApiController
{
    public function __construct(private readonly AuthService $authService) {}

    /** Emails a 4-digit OTP to be used with the reset-password endpoint; throttled per email. */
    public function __invoke(ForgotPasswordRequest $request)
    {
        $retryAfterSeconds = $this->authService->sendOtp($request->input('email'));

        if ($retryAfterSeconds !== null) {
            return $this->error(
                __('messages.auth.otp_throttled', ['seconds' => $retryAfterSeconds]),
                429
            );
        }

        return $this->success(null, __('messages.auth.otp_sent'));
    }
}
