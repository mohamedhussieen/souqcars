<?php

namespace App\Http\Controllers\Api\Admin\Users;

use App\Enums\UserRole;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\UpdateUserRoleRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AdminUserService;

/** Replaces a user's assigned role. */
class UpdateUserRoleController extends BaseApiController
{
    public function __construct(private readonly AdminUserService $adminUserService) {}

    /** Updates the role for the given user (route-model bound). */
    public function __invoke(UpdateUserRoleRequest $request, User $user)
    {
        $updated = $this->adminUserService->updateRole(
            $user,
            UserRole::from($request->input('role'))
        );

        return $this->success(
            new UserResource($updated),
            __('messages.admin.user_role_updated')
        );
    }
}
