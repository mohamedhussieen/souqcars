<?php

namespace App\Http\Controllers\Api\Admin\Cars;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\UploadInspectionRequest;
use App\Models\Car;
use App\Services\CarService;

/** Uploads (replacing any existing) the inspection report file for a car. */
class UploadInspectionController extends BaseApiController
{
    public function __construct(private readonly CarService $carService) {}

    /** Stores the report, flags the car as inspected, and returns the file URL. */
    public function __invoke(UploadInspectionRequest $request, Car $car)
    {
        $this->carService->uploadInspectionReport($car, $request->file('file'));

        return $this->success([
            'inspection_url' => $car->fresh()->inspection_report_url,
        ], __('messages.admin.inspection_uploaded'));
    }
}
