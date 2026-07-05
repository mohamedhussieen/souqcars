<?php

namespace App\Http\Controllers\Api\Mobile\Profile;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

/** Returns the authenticated user's profile data. */
class ShowProfileController extends BaseApiController
{
    /** Fetches and returns the current authenticated user via UserResource. */
    public function __invoke(Request $request)
    {
        return $this->success(
            new UserResource($request->user()),
            __('messages.profile.fetched')
        );
    }
}
