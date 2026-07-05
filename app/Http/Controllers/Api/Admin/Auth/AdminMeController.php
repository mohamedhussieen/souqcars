<?php

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

/** Returns the authenticated admin's profile data. */
class AdminMeController extends BaseApiController
{
    /** Fetches and returns the current authenticated admin via UserResource. */
    public function __invoke(Request $request)
    {
        return $this->success(
            new UserResource($request->user()),
            __('messages.profile.fetched')
        );
    }
}
