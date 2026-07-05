<?php

namespace App\Services;

use App\Models\CarModel;
use Illuminate\Pagination\LengthAwarePaginator;

/** Handles all admin-dashboard operations for managing car models. */
class AdminCarModelService
{
    /** Returns a paginated, optionally brand-filtered list of car models ordered by newest first. */
    public function list(int $perPage, ?int $brandId): LengthAwarePaginator
    {
        return CarModel::query()
            ->when($brandId, fn ($query) => $query->where('brand_id', $brandId))
            ->latest()
            ->paginate($perPage);
    }

    /** Creates a new car model under the given brand. */
    public function create(array $data): CarModel
    {
        return CarModel::create($data);
    }

    /** Updates the given car model's brand and bilingual names. */
    public function update(CarModel $carModel, array $data): CarModel
    {
        $carModel->update($data);

        return $carModel->fresh();
    }

    /** Deletes the given car model. */
    public function delete(CarModel $carModel): void
    {
        $carModel->delete();
    }
}
