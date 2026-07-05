<?php

namespace App\Http\Controllers\Api\Mobile\Profile;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\ProfileService;
use Illuminate\Http\Request;

/** Soft-deletes the authenticated user's account and revokes all tokens. */
class DeleteAccountController extends BaseApiController
{
    public function __construct(private readonly ProfileService $profileService) {}

    /** Removes the account and all associated tokens, returning a success response. */
    public function __invoke(Request $request)
    {
        $this->profileService->deleteAccount($request->user());

        return $this->success(null, __('messages.profile.deleted'));
    }
}
