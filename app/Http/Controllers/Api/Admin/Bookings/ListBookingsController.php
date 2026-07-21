<?php

namespace App\Http\Controllers\Api\Admin\Bookings;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Lookup\PaginationRequest;
use App\Http\Resources\Admin\BookingAdminResource;
use App\Services\BookingService;

/** Returns a paginated, filterable admin list of all bookings. */
class ListBookingsController extends BaseApiController
{
    public function __construct(private readonly BookingService $service)
    {
    }

    /** Fetches bookings filtered by status/center/date range/search. */
    public function __invoke(PaginationRequest $request)
    {
        $paginator = $this->service->adminList($request->perPage(), [
            'status'    => $request->input('status'),
            'center_id' => $request->input('center_id'),
            'date_from' => $request->input('date_from'),
            'date_to'   => $request->input('date_to'),
            'search'    => $request->input('search'),
        ]);

        $paginator->setCollection(
            collect(BookingAdminResource::collection($paginator->getCollection())->toArray($request))
        );

        return $this->successPaginated($paginator, __('messages.admin.bookings_fetched'));
    }
}
