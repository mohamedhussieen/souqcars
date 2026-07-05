<?php

namespace App\Http\Controllers\Api\Admin\Users;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\UserResource;
use App\Models\User;

/** Returns a single user's details for the admin dashboard. */
class ShowUserController extends BaseApiController
{
    /** Fetches the given user (route-model bound) via UserResource. */
    public function __invoke(User $user)
    {
        return $this->success(
            new UserResource($user),
            __('messages.admin.user_fetched')
        );
    }
}
