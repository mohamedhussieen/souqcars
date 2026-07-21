<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\NotificationType;
use App\Enums\UserRole;
use App\Exceptions\BookingConflictException;
use App\Exceptions\BookingNotCancellableException;
use App\Exceptions\InvalidBookingStatusTransitionException;
use App\Exceptions\NotOwnerException;
use App\Models\Booking;
use App\Models\MaintenanceService;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

/** Handles the maintenance-booking lifecycle: creation, cancellation, and admin status transitions. */
class BookingService
{
    /** Maps each booking status to the set of statuses it may transition to. */
    private const ALLOWED_TRANSITIONS = [
        'pending'   => ['confirmed', 'cancelled'],
        'confirmed' => ['completed', 'cancelled'],
    ];

    public function __construct(private readonly NotificationService $notificationService)
    {
    }

    /**
     * Creates a booking for the given user, snapshotting the service's current price.
     * Throws BookingConflictException if the user already has a pending/confirmed booking at the
     * same center on the same date.
     */
    public function create(User $user, array $data): Booking
    {
        $conflict = Booking::query()
            ->where('user_id', $user->id)
            ->where('maintenance_center_id', $data['maintenance_center_id'])
            ->where('date', $data['date'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($conflict) {
            throw new BookingConflictException();
        }

        $service = MaintenanceService::findOrFail($data['maintenance_service_id']);

        $booking = Booking::create([
            'user_id'                => $user->id,
            'maintenance_center_id'  => $data['maintenance_center_id'],
            'maintenance_service_id' => $data['maintenance_service_id'],
            'car_id'                 => $data['car_id'] ?? null,
            'status'                 => BookingStatus::Pending->value,
            'date'                   => $data['date'],
            'time'                   => $data['time'],
            'price'                  => $service->price,
            'notes'                  => $data['notes'] ?? null,
        ]);

        $this->notificationService->send($user, NotificationType::BookingConfirmed, [
            'booking_id' => $booking->id,
        ]);

        return $booking->fresh();
    }

    /**
     * Cancels a booking. The actor must be the booking's owner or an admin.
     * Throws NotOwnerException (403) if neither, BookingNotCancellableException (422) if the
     * booking isn't pending/confirmed.
     */
    public function cancel(Booking $booking, ?string $reason, User $actor): Booking
    {
        $isOwner = $booking->user_id === $actor->id;
        $isAdmin = $actor->hasRole(UserRole::Admin->value);

        if (!$isOwner && !$isAdmin) {
            throw new NotOwnerException('messages.bookings.forbidden');
        }

        if (!in_array($booking->status, [BookingStatus::Pending, BookingStatus::Confirmed], true)) {
            throw new BookingNotCancellableException();
        }

        $booking->update([
            'status'               => BookingStatus::Cancelled->value,
            'cancellation_reason'  => $reason,
        ]);

        if ($isAdmin && !$isOwner) {
            $this->notificationService->send($booking->user, NotificationType::BookingCancelled, [
                'booking_id' => $booking->id,
            ]);
        }

        return $booking->fresh();
    }

    /**
     * Updates a booking's status (admin only). Throws InvalidBookingStatusTransitionException if
     * the transition isn't allowed from the booking's current status.
     */
    public function updateStatus(Booking $booking, BookingStatus $status): Booking
    {
        $allowed = self::ALLOWED_TRANSITIONS[$booking->status->value] ?? [];

        if (!in_array($status->value, $allowed, true)) {
            throw new InvalidBookingStatusTransitionException();
        }

        $booking->update(['status' => $status->value]);

        $type = match ($status) {
            BookingStatus::Confirmed => NotificationType::BookingConfirmed,
            BookingStatus::Cancelled => NotificationType::BookingCancelled,
            default => null,
        };

        if ($type !== null) {
            $this->notificationService->send($booking->user, $type, ['booking_id' => $booking->id]);
        }

        return $booking->fresh();
    }

    /** Returns a paginated list of the user's own bookings, newest first. */
    public function list(User $user, int $perPage): LengthAwarePaginator
    {
        return Booking::query()
            ->where('user_id', $user->id)
            ->with(['maintenanceService', 'maintenanceCenter', 'car'])
            ->latest()
            ->paginate($perPage);
    }

    /** Returns a paginated, filtered admin list of all bookings. */
    public function adminList(int $perPage, array $filters): LengthAwarePaginator
    {
        return Booking::query()
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['center_id'] ?? null, fn ($query, $centerId) => $query->where('maintenance_center_id', $centerId))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('date', '<=', $date))
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->whereHas('user', fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")))
            ->with(['user', 'maintenanceService', 'maintenanceCenter', 'car'])
            ->latest()
            ->paginate($perPage);
    }
}
