<?php

namespace App\Http\Controllers\Api\Mobile\Lookup;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Lookup\PaginationRequest;
use App\Http\Resources\ColorResource;
use App\Models\Color;

/** Returns a paginated list of active car colors. */
class ColorsController extends BaseApiController
{
    /** Fetches active colors paginated and locale-aware. */
    public function __invoke(PaginationRequest $request)
    {
        $paginator = Color::query()->where('is_active', true)->paginate($request->perPage());

        $paginator->setCollection(
            collect(ColorResource::collection($paginator->getCollection())->toArray($request))
        );

        return $this->successPaginated($paginator, __('messages.lookup.colors_fetched'));
    }
}
