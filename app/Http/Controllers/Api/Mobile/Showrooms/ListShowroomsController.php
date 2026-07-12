<?php

namespace App\Http\Controllers\Api\Mobile\Showrooms;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Lookup\PaginationRequest;
use App\Http\Resources\ShowroomResource;
use App\Models\Showroom;

/** Returns a paginated list of active showrooms ordered by rating. */
class ListShowroomsController extends BaseApiController
{
    /** Fetches active showrooms, best-rated first. */
    public function __invoke(PaginationRequest $request)
    {
        $paginator = Showroom::query()
            ->where('is_active', true)
            ->withCount('cars')
            ->orderByDesc('rating')
            ->paginate($request->perPage());

        $paginator->setCollection(
            collect(ShowroomResource::collection($paginator->getCollection())->toArray($request))
        );

        return $this->successPaginated($paginator, __('messages.showrooms.fetched'));
    }
}
