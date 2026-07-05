<?php

namespace App\Http\Controllers\Api\Admin\Users;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AdminUserService;

/** Toggles a user's active status, revoking their tokens when deactivated. */
class ToggleUserActiveController extends BaseApiController
{
    public function __construct(private readonly AdminUserService $adminUserService) {}

    /** Flips the is_active flag for the given user (route-model bound). */
    public function __invoke(User $user)
    {
        $updated = $this->adminUserService->toggleActive($user);

        return $this->success(
            new UserResource($updated),
            __('messages.admin.user_status_updated')
        );
    }
}
