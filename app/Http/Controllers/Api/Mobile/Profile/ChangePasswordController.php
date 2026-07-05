<?php

namespace App\Http\Controllers\Api\Mobile\Profile;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Services\ProfileService;

/** Allows the authenticated user to change their account password. */
class ChangePasswordController extends BaseApiController
{
    public function __construct(private readonly ProfileService $profileService) {}

    /** Changes the password after verifying the current one; returns 400 if verification fails. */
    public function __invoke(ChangePasswordRequest $request)
    {
        $changed = $this->profileService->changePassword(
            $request->user(),
            $request->input('current_password'),
            $request->input('password')
        );

        if (!$changed) {
            return $this->error(__('messages.profile.wrong_password'));
        }

        return $this->success(null, __('messages.profile.password_changed'));
    }
}
