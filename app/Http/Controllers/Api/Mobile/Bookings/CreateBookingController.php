<?php

namespace App\Http\Controllers\Api\Mobile\Bookings;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Mobile\CreateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Services\BookingService;

/** Creates a new maintenance booking for the authenticated user. */
class CreateBookingController extends BaseApiController
{
    public function __construct(private readonly BookingService $service)
    {
    }

    /** Validates and creates the booking, returning it with a 201 status. */
    public function __invoke(CreateBookingRequest $request)
    {
        $booking = $this->service->create($request->user(), $request->validated());

        return $this->success(new BookingResource($booking), __('messages.bookings.created'), 201);
    }
}
