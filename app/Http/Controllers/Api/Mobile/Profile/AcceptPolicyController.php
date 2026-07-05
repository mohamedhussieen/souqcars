<?php

namespace App\Http\Controllers\Api\Mobile\Profile;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\UserResource;
use App\Services\ProfileService;
use Illuminate\Http\Request;

/** Records the authenticated user's acceptance of the application policy. */
class AcceptPolicyController extends BaseApiController
{
    public function __construct(private readonly ProfileService $profileService) {}

    /** Marks the policy as accepted and returns the updated user resource. */
    public function __invoke(Request $request)
    {
        $user = $this->profileService->acceptPolicy($request->user());

        return $this->success(
            new UserResource($user),
            __('messages.profile.policy_accepted')
        );
    }
}
