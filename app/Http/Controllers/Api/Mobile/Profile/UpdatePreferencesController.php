<?php

namespace App\Http\Controllers\Api\Mobile\Profile;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Profile\UpdatePreferencesRequest;
use App\Http\Resources\UserResource;
use App\Services\ProfileService;

/** Updates the authenticated user's notification and theme preferences. */
class UpdatePreferencesController extends BaseApiController
{
    public function __construct(private readonly ProfileService $profileService) {}

    /** Persists the preference changes and returns the updated user resource. */
    public function __invoke(UpdatePreferencesRequest $request)
    {
        $user = $this->profileService->updatePreferences(
            $request->user(),
            (bool) $request->input('notification_enabled'),
            $request->input('theme')
        );

        return $this->success(
            new UserResource($user),
            __('messages.profile.preferences_updated')
        );
    }
}
