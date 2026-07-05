<?php

namespace App\Http\Controllers\Api\Admin\Users;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\AdminUserListRequest;
use App\Http\Resources\UserResource;
use App\Services\AdminUserService;

/** Returns a paginated, searchable, role-filterable list of marketplace users. */
class ListUsersController extends BaseApiController
{
    public function __construct(private readonly AdminUserService $adminUserService) {}

    /** Fetches users paginated via AdminUserService. */
    public function __invoke(AdminUserListRequest $request)
    {
        $paginator = $this->adminUserService->list(
            $request->perPage(),
            $request->input('search'),
            $request->input('role')
        );

        $paginator->setCollection(
            collect(UserResource::collection($paginator->getCollection())->toArray($request))
        );

        return $this->successPaginated($paginator, __('messages.admin.users_fetched'));
    }
}
