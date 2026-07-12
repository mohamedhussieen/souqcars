<?php

namespace App\Http\Controllers\Api\Admin\Colors;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\UpdateColorRequest;
use App\Http\Resources\Admin\ColorAdminResource;
use App\Models\Color;

/** Updates an existing car color. */
class UpdateColorController extends BaseApiController
{
    /** Applies the validated changes and returns the updated color. */
    public function __invoke(UpdateColorRequest $request, Color $color)
    {
        $color->update($request->validated());

        return $this->success(new ColorAdminResource($color), __('messages.admin.color_updated'));
    }
}
