<?php

namespace App\Http\Controllers\Api\Mobile\Profile;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\ProfileService;

/** Updates the authenticated user's name, phone, and optional avatar. */
class UpdateProfileController extends BaseApiController
{
    public function __construct(private readonly ProfileService $profileService) {}

    /** Persists profile changes and returns the updated user resource. */
    public function __invoke(UpdateProfileRequest $request)
    {
        $user = $this->profileService->update(
            $request->user(),
            $request->validated(),
            $request->file('avatar')
        );

        return $this->success(
            new UserResource($user),
            __('messages.profile.updated')
        );
    }
}
