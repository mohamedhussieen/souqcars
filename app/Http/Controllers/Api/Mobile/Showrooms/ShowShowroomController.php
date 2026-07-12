<?php

namespace App\Http\Controllers\Api\Mobile\Showrooms;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\ShowroomResource;
use App\Models\Showroom;

/** Returns a single showroom's public profile. */
class ShowShowroomController extends BaseApiController
{
    /** Shows the showroom with its listed-cars count. */
    public function __invoke(Showroom $showroom)
    {
        $showroom->loadCount('cars');

        return $this->success(new ShowroomResource($showroom), __('messages.showrooms.detail_fetched'));
    }
}
