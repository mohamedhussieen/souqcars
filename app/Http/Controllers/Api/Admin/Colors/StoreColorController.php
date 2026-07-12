<?php

namespace App\Http\Controllers\Api\Admin\Colors;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\CreateColorRequest;
use App\Http\Resources\Admin\ColorAdminResource;
use App\Models\Color;

/** Creates a new car color. */
class StoreColorController extends BaseApiController
{
    /** Persists the color and returns it. */
    public function __invoke(CreateColorRequest $request)
    {
        $color = Color::create($request->validated());

        return $this->success(new ColorAdminResource($color), __('messages.admin.color_created'), 201);
    }
}
