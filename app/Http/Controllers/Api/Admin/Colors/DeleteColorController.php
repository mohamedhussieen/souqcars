<?php

namespace App\Http\Controllers\Api\Admin\Colors;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Color;

/** Deletes a car color if no car currently references it. */
class DeleteColorController extends BaseApiController
{
    /** Refuses deletion with 422 while any car uses this color. */
    public function __invoke(Color $color)
    {
        if ($color->cars()->exists()) {
            return $this->error(__('messages.admin.color_in_use'), 422);
        }

        $color->delete();

        return $this->success(null, __('messages.admin.color_deleted'));
    }
}
