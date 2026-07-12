<?php

namespace App\Http\Controllers\Api\Admin\Cars;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\UploadCarImagesRequest;
use App\Models\Car;
use App\Services\CarService;

/** Uploads gallery images to a car (combined max 10 enforced by CarService). */
class UploadCarImagesController extends BaseApiController
{
    public function __construct(private readonly CarService $carService) {}

    /** Adds the images and returns the uploaded/total counts. */
    public function __invoke(UploadCarImagesRequest $request, Car $car)
    {
        $files = $request->file('images', []);

        $this->carService->uploadImages($car, $files);

        return $this->success([
            'uploaded_count' => count($files),
            'total_count'    => $car->getMedia('car_images')->count(),
        ], __('messages.admin.car_images_uploaded'));
    }
}
