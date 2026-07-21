<?php

namespace App\Http\Controllers\Api\Admin\Users;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Admin\UserAdminResource;
use App\Models\User;
use App\Services\AdminUserService;

/** Deactivates a user and revokes all their Sanctum tokens. */
class BanUserController extends BaseApiController
{
    public function __construct(private readonly AdminUserService $adminUserService)
    {
    }

    /** Bans the given user (route-model bound). */
    public function __invoke(User $user)
    {
        $banned = $this->adminUserService->ban($user);

        return $this->success(new UserAdminResource($banned), __('messages.admin.user_status_updated'));
    }
}
