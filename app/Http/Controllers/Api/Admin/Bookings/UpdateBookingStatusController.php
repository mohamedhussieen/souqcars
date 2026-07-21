<?php

namespace App\Http\Controllers\Api\Admin\Bookings;

use App\Enums\BookingStatus;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\UpdateBookingStatusRequest;
use App\Http\Resources\Admin\BookingAdminResource;
use App\Models\Booking;
use App\Services\BookingService;

/** Updates a booking's status (admin only), enforcing valid transitions. */
class UpdateBookingStatusController extends BaseApiController
{
    public function __construct(private readonly BookingService $service)
    {
    }

    /** Applies the status transition and returns the updated booking. */
    public function __invoke(UpdateBookingStatusRequest $request, Booking $booking)
    {
        $updated = $this->service->updateStatus($booking, BookingStatus::from($request->validated('status')));

        return $this->success(new BookingAdminResource($updated), __('messages.admin.booking_status_updated'));
    }
}
