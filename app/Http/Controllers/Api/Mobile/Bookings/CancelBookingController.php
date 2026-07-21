<?php

namespace App\Http\Controllers\Api\Mobile\Bookings;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Mobile\CancelBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;

/** Cancels a booking on behalf of the authenticated user (must be the owner). */
class CancelBookingController extends BaseApiController
{
    public function __construct(private readonly BookingService $service)
    {
    }

    /** Cancels the booking with an optional reason. */
    public function __invoke(CancelBookingRequest $request, Booking $booking)
    {
        $cancelled = $this->service->cancel($booking, $request->validated('cancellation_reason'), $request->user());

        return $this->success(new BookingResource($cancelled), __('messages.bookings.cancelled'));
    }
}
