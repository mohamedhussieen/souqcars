<?php

namespace App\Services;

use App\Enums\CarStatus;
use App\Enums\NotificationType;
use App\Exceptions\InvalidCarStateException;
use App\Models\Car;
use App\Models\CarWatchRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/** Handles "notify me" watch requests for sold-out brand/model combinations. */
class WatchRequestService
{
    public function __construct(private readonly NotificationService $notificationService)
    {
    }

    /** Creates (or reactivates) a watch request for the car's brand/model. Throws InvalidCarStateException (422) if the car isn't sold. */
    public function watch(User $user, Car $car): CarWatchRequest
    {
        if ($car->status !== CarStatus::Sold) {
            throw new InvalidCarStateException('messages.watch_requests.not_sold');
        }

        $watchRequest = CarWatchRequest::query()
            ->where('user_id', $user->id)
            ->where('brand_id', $car->brand_id)
            ->where('car_model_id', $car->car_model_id)
            ->first();

        if ($watchRequest) {
            $watchRequest->update(['is_active' => true]);

            return $watchRequest->fresh();
        }

        return CarWatchRequest::create([
            'user_id'      => $user->id,
            'brand_id'     => $car->brand_id,
            'car_model_id' => $car->car_model_id,
            'is_active'    => true,
        ]);
    }

    /** Deactivates the user's watch request for the car's brand/model, if any. */
    public function unwatch(User $user, Car $car): void
    {
        CarWatchRequest::query()
            ->where('user_id', $user->id)
            ->where('brand_id', $car->brand_id)
            ->where('car_model_id', $car->car_model_id)
            ->update(['is_active' => false]);
    }

    /** Returns all of the user's watch requests, newest first. */
    public function list(User $user): Collection
    {
        return CarWatchRequest::query()
            ->where('user_id', $user->id)
            ->with(['brand', 'carModel'])
            ->latest()
            ->get();
    }

    /**
     * Notifies every user with an active watch request matching this car's brand/model.
     * Intended to be called only from the queued SendWatchNotificationsJob.
     */
    public function notifyMatches(Car $car): void
    {
        $watchRequests = CarWatchRequest::query()
            ->where('brand_id', $car->brand_id)
            ->where('car_model_id', $car->car_model_id)
            ->where('is_active', true)
            ->with('user')
            ->get();

        foreach ($watchRequests as $watchRequest) {
            $this->notificationService->send($watchRequest->user, NotificationType::CarAvailable, [
                'car_id' => $car->id,
            ]);

            $watchRequest->update(['notified_at' => now()]);
        }
    }

    /** Returns a paginated, grouped overview of watch-request demand by brand+model for the admin dashboard. */
    public function adminOverview(int $perPage): LengthAwarePaginator
    {
        return CarWatchRequest::query()
            ->selectRaw('brand_id, car_model_id, COUNT(*) as watchers_count, MAX(created_at) as latest_request_at')
            ->where('is_active', true)
            ->groupBy('brand_id', 'car_model_id')
            ->orderByDesc('watchers_count')
            ->with(['brand', 'carModel'])
            ->paginate($perPage);
    }
}
