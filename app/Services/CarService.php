<?php

namespace App\Services;

use App\Enums\CarStatus;
use App\Exceptions\CarImageLimitExceededException;
use App\Models\Car;
use Illuminate\Http\UploadedFile;

/** Handles all car listing lifecycle operations: creation, media, status, stats. */
class CarService
{
    /** Maximum number of images a single car listing may have in its gallery. */
    private const MAX_IMAGES = 10;

    /** Creates a new car listing, optionally with gallery images and an inspection report. */
    public function create(array $data, array $imageFiles = [], ?UploadedFile $inspectionFile = null): Car
    {
        $car = Car::create($data);

        if (!empty($imageFiles)) {
            $this->uploadImages($car, $imageFiles);
        }

        if ($inspectionFile) {
            $this->uploadInspectionReport($car, $inspectionFile);
        }

        return $car->fresh();
    }

    /** Updates the given car's attributes. */
    public function update(Car $car, array $data): Car
    {
        $car->update($data);

        return $car->fresh();
    }

    /** Soft-deletes the given car. */
    public function delete(Car $car): void
    {
        $car->delete();
    }

    /**
     * Uploads new gallery images for the car, enforcing a combined (existing + new) max of 10.
     *
     * @throws CarImageLimitExceededException
     */
    public function uploadImages(Car $car, array $files): void
    {
        $existingCount = $car->getMedia('car_images')->count();

        if ($existingCount + count($files) > self::MAX_IMAGES) {
            throw new CarImageLimitExceededException();
        }

        foreach ($files as $file) {
            $car->addMedia($file)->toMediaCollection('car_images');
        }

        // addMedia() persists to the database but does not invalidate the cached
        // 'media' relation on this instance, so a subsequent getMedia() call
        // (e.g. immediately re-checking the count) would otherwise return stale data.
        $car->unsetRelation('media');
    }

    /** Deletes a single gallery image belonging to the car by its media id. */
    public function deleteImage(Car $car, int $mediaId): void
    {
        $media = $car->getMedia('car_images')->firstWhere('id', $mediaId);

        $media?->delete();
    }

    /** Uploads (replacing any existing) the inspection report and flags the car as inspected. */
    public function uploadInspectionReport(Car $car, $file): void
    {
        $car->addMedia($file)->toMediaCollection('inspection_report');
        $car->update(['has_inspection_report' => true]);
    }

    /** Marks the car as sold. */
    public function markAsSold(Car $car): void
    {
        $car->update(['status' => CarStatus::Sold]);
    }

    /** Increments the car's view counter by one. */
    public function incrementViews(Car $car): void
    {
        $car->increment('views_count');
    }

    /** Recalculates and persists the car's average rating from its associated CarRating rows. */
    public function updateRatingAvg(Car $car): void
    {
        $average = $car->ratings()->avg('rating') ?? 0;

        $car->update(['rating_avg' => round($average, 2)]);
    }
}
