<?php

namespace App\Http\Controllers\Api\Mobile\Bookings;

use App\Exceptions\NotOwnerException;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\BookingDetailResource;
use App\Models\Booking;
use Illuminate\Http\Request;

/** Returns a single booking's detail, restricted to its owner. */
class ShowBookingController extends BaseApiController
{
    /** Shows the booking with its center/service/car relations loaded. Throws NotOwnerException (403) if not owned by the requester. */
    public function __invoke(Request $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id) {
            throw new NotOwnerException('messages.bookings.forbidden');
        }

        $booking->load(['maintenanceCenter', 'maintenanceService', 'car']);

        return $this->success(new BookingDetailResource($booking), __('messages.bookings.detail_fetched'));
    }
}
