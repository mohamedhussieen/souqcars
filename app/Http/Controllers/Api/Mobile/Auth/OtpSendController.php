<?php

namespace App\Http\Controllers\Api\Mobile\Auth;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Auth\OtpSendRequest;
use App\Services\AuthService;

/** Generates and dispatches an OTP code to the given email address. */
class OtpSendController extends BaseApiController
{
    public function __construct(private readonly AuthService $authService) {}

    /** Emails a 4-digit OTP with a 5-minute expiry to the provided email; throttled per email. */
    public function __invoke(OtpSendRequest $request)
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
