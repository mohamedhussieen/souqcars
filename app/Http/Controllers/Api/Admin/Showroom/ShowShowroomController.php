<?php

namespace App\Http\Controllers\Api\Admin\Showroom;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Admin\ShowroomAdminResource;
use App\Services\ShowroomService;

/** Returns the single showroom profile for the admin dashboard. */
class ShowShowroomController extends BaseApiController
{
    public function __construct(private readonly ShowroomService $showroomService) {}

    /** Fetches (lazily creating) the showroom row. */
    public function __invoke()
    {
        $showroom = $this->showroomService->get();

        return $this->success(new ShowroomAdminResource($showroom), __('messages.admin.showroom_fetched'));
    }
}
