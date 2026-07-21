<?php

namespace App\Http\Controllers\Api\Admin\Users;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Admin\UserAdminResource;
use App\Models\User;
use App\Services\AdminUserService;

/** Reactivates a previously banned user. */
class UnbanUserController extends BaseApiController
{
    public function __construct(private readonly AdminUserService $adminUserService)
    {
    }

    /** Unbans the given user (route-model bound). */
    public function __invoke(User $user)
    {
        $unbanned = $this->adminUserService->unban($user);

        return $this->success(new UserAdminResource($unbanned), __('messages.admin.user_status_updated'));
    }
}
