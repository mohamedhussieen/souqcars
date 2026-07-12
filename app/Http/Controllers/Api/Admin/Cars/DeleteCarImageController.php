<?php

namespace App\Http\Controllers\Api\Admin\Cars;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Car;
use App\Services\CarService;

/** Deletes a single gallery image from a car by media id. */
class DeleteCarImageController extends BaseApiController
{
    public function __construct(private readonly CarService $carService) {}

    /** Removes the image and returns a confirmation. */
    public function __invoke(Car $car, int $mediaId)
    {
        $this->carService->deleteImage($car, $mediaId);

        return $this->success(null, __('messages.admin.car_image_deleted'));
    }
}
