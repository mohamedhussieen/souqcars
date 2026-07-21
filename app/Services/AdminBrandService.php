<?php

namespace App\Services;

use App\Exceptions\HasDependentRecordsException;
use App\Models\Brand;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

/** Handles all admin-dashboard operations for managing car brands. */
class AdminBrandService
{
    /** Returns a paginated list of brands ordered by newest first. */
    public function list(int $perPage): LengthAwarePaginator
    {
        return Brand::query()->latest()->paginate($perPage);
    }

    /** Creates a new brand and optionally attaches its logo via Media Library. */
    public function create(array $data, ?UploadedFile $logo = null): Brand
    {
        $brand = Brand::create($data);

        if ($logo) {
            $brand->addMedia($logo)->toMediaCollection('logo');
        }

        return $brand->fresh();
    }

    /** Updates the given brand's names and optionally replaces its logo. */
    public function update(Brand $brand, array $data, ?UploadedFile $logo = null): Brand
    {
        $brand->update($data);

        if ($logo) {
            $brand->addMedia($logo)->toMediaCollection('logo');
        }

        return $brand->fresh();
    }

    /** Deletes the given brand. Throws HasDependentRecordsException if it has any cars. */
    public function delete(Brand $brand): void
    {
        if ($brand->cars()->exists()) {
            throw new HasDependentRecordsException('messages.admin.brand_has_cars');
        }

        $brand->delete();
    }
}
