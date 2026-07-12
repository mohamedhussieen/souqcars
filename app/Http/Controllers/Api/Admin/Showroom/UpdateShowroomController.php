<?php

namespace App\Http\Controllers\Api\Admin\Showroom;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\UpdateShowroomRequest;
use App\Http\Resources\Admin\ShowroomAdminResource;
use App\Services\ShowroomService;

/** Updates the single showroom profile. */
class UpdateShowroomController extends BaseApiController
{
    public function __construct(private readonly ShowroomService $showroomService) {}

    /** Applies the validated changes and returns the updated showroom. */
    public function __invoke(UpdateShowroomRequest $request)
    {
        $showroom = $this->showroomService->update(
            $this->showroomService->get(),
            $request->validated()
        );

        return $this->success(new ShowroomAdminResource($showroom), __('messages.admin.showroom_updated'));
    }
}
