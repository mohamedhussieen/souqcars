<?php

namespace App\Http\Controllers\Api\Mobile\Auth;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Auth\OtpVerifyRequest;
use App\Services\AuthService;

/** Verifies a submitted OTP code against the stored value. */
class OtpVerifyController extends BaseApiController
{
    public function __construct(private readonly AuthService $authService) {}

    /** Returns success if the OTP is valid, or a 400 error if invalid or expired. */
    public function __invoke(OtpVerifyRequest $request)
    {
        $verified = $this->authService->verifyOtp(
            $request->input('email'),
            $request->input('otp_code')
        );

        if (!$verified) {
            return $this->error(__('messages.auth.otp_invalid'));
        }

        return $this->success(null, __('messages.auth.otp_verified'));
    }
}
