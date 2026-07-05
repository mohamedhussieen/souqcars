<?php

namespace App\Http\Controllers\Api\Admin\Users;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\User;
use App\Services\AdminUserService;

/** Soft-deletes a user account and revokes their tokens. */
class DeleteUserController extends BaseApiController
{
    public function __construct(private readonly AdminUserService $adminUserService) {}

    /** Deletes the given user (route-model bound). */
    public function __invoke(User $user)
    {
        $this->adminUserService->delete($user);

        return $this->success(null, __('messages.admin.user_deleted'));
    }
}
