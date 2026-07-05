<?php

namespace App\Http\Controllers\Api\Mobile\Auth;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Auth\OtpSendRequest;
use App\Services\AuthService;

/** Generates and dispatches an OTP code to the given email address. */
class OtpSendController extends BaseApiController
{
    public function __construct(private readonly AuthService $authService) {}

    /** Emails a 4-digit OTP with a 5-minute expiry to the provided email. */
    public function __invoke(OtpSendRequest $request)
    {
        $this->authService->sendOtp($request->input('email'));

        return $this->success(null, __('messages.auth.otp_sent'));
    }
}
