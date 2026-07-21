<?php

namespace App\Services;

use App\Exceptions\HasDependentRecordsException;
use App\Models\City;
use Illuminate\Pagination\LengthAwarePaginator;

/** Handles all admin-dashboard operations for managing cities. */
class AdminCityService
{
    /** Returns a paginated list of cities ordered by newest first. */
    public function list(int $perPage): LengthAwarePaginator
    {
        return City::query()->latest()->paginate($perPage);
    }

    /** Creates a new city. */
    public function create(array $data): City
    {
        return City::create($data);
    }

    /** Updates the given city's bilingual names. */
    public function update(City $city, array $data): City
    {
        $city->update($data);

        return $city->fresh();
    }

    /** Deletes the given city. Throws HasDependentRecordsException if it has any cars. */
    public function delete(City $city): void
    {
        if ($city->cars()->exists()) {
            throw new HasDependentRecordsException('messages.admin.city_has_cars');
        }

        $city->delete();
    }
}
