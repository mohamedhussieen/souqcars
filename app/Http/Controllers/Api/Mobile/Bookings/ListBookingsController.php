<?php

namespace App\Http\Controllers\Api\Mobile\Bookings;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Lookup\PaginationRequest;
use App\Http\Resources\BookingResource;
use App\Services\BookingService;

/** Returns a paginated list of the authenticated user's own bookings. */
class ListBookingsController extends BaseApiController
{
    public function __construct(private readonly BookingService $service)
    {
    }

    /** Fetches the user's bookings, newest first. */
    public function __invoke(PaginationRequest $request)
    {
        $paginator = $this->service->list($request->user(), $request->perPage());

        $paginator->setCollection(
            collect(BookingResource::collection($paginator->getCollection())->toArray($request))
        );

        return $this->successPaginated($paginator, __('messages.bookings.fetched'));
    }
}
