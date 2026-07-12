<?php

namespace App\Http\Controllers\Api\Admin\Colors;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Lookup\PaginationRequest;
use App\Http\Resources\Admin\ColorAdminResource;
use App\Models\Color;

/** Returns all colors paginated for the admin dashboard. */
class ListColorsController extends BaseApiController
{
    /** Fetches colors paginated with both language fields. */
    public function __invoke(PaginationRequest $request)
    {
        $paginator = Color::query()->latest()->paginate($request->perPage());

        $paginator->setCollection(
            collect(ColorAdminResource::collection($paginator->getCollection())->toArray($request))
        );

        return $this->successPaginated($paginator, __('messages.admin.colors_fetched'));
    }
}
