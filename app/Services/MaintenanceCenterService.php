<?php

namespace App\Services;

use App\Exceptions\HasDependentRecordsException;
use App\Models\Booking;
use App\Models\MaintenanceCenter;
use App\Models\MaintenanceService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

/** Handles maintenance-center and maintenance-service CRUD shared by mobile (read-only) and admin flows. */
class MaintenanceCenterService
{
    /** Returns a paginated list of active maintenance centers, newest first. */
    public function list(int $perPage): LengthAwarePaginator
    {
        return MaintenanceCenter::query()
            ->where('is_active', true)
            ->latest()
            ->paginate($perPage);
    }

    /** Returns a paginated, optionally search/active-filtered admin list of all maintenance centers. */
    public function adminList(int $perPage, ?string $search, ?bool $isActive): LengthAwarePaginator
    {
        return MaintenanceCenter::query()
            ->when($search, fn ($query) => $query->where(fn ($q) => $q
                ->where('name_ar', 'like', "%{$search}%")
                ->orWhere('name_en', 'like', "%{$search}%")))
            ->when($isActive !== null, fn ($query) => $query->where('is_active', $isActive))
            ->latest()
            ->paginate($perPage);
    }

    /** Returns a single maintenance center. */
    public function get(MaintenanceCenter $center): MaintenanceCenter
    {
        return $center;
    }

    /** Returns the center's active services, ordered by sort_order. */
    public function services(MaintenanceCenter $center): Collection
    {
        return $center->services()->where('is_active', true)->orderBy('sort_order')->get();
    }

    /** Creates a new maintenance center and optionally attaches its logo via Media Library. */
    public function create(array $data, ?UploadedFile $logo = null): MaintenanceCenter
    {
        $center = MaintenanceCenter::create($data);

        if ($logo) {
            $center->addMedia($logo)->toMediaCollection('center_logo');
        }

        return $center->fresh();
    }

    /** Updates the given maintenance center and optionally replaces its logo. */
    public function update(MaintenanceCenter $center, array $data, ?UploadedFile $logo = null): MaintenanceCenter
    {
        $center->update($data);

        if ($logo) {
            $center->addMedia($logo)->toMediaCollection('center_logo');
        }

        return $center->fresh();
    }

    /** Deletes the given maintenance center. Throws HasDependentRecordsException if it has pending/confirmed bookings. */
    public function delete(MaintenanceCenter $center): void
    {
        $this->ensureNoActiveBookings($center);

        $center->delete();
    }

    /** Creates a new service under the given maintenance center. */
    public function createService(MaintenanceCenter $center, array $data): MaintenanceService
    {
        return $center->services()->create($data);
    }

    /** Updates the given maintenance service. */
    public function updateService(MaintenanceService $service, array $data): MaintenanceService
    {
        $service->update($data);

        return $service->fresh();
    }

    /** Deletes the given maintenance service. Throws HasDependentRecordsException if it has pending/confirmed bookings. */
    public function deleteService(MaintenanceService $service): void
    {
        $hasActiveBookings = Booking::query()
            ->where('maintenance_service_id', $service->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($hasActiveBookings) {
            throw new HasDependentRecordsException('messages.admin.maintenance_service_has_bookings');
        }

        $service->delete();
    }

    /** Throws HasDependentRecordsException if the center has any pending/confirmed bookings. */
    private function ensureNoActiveBookings(MaintenanceCenter $center): void
    {
        $hasActiveBookings = Booking::query()
            ->where('maintenance_center_id', $center->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($hasActiveBookings) {
            throw new HasDependentRecordsException('messages.admin.maintenance_center_has_bookings');
        }
    }
}
