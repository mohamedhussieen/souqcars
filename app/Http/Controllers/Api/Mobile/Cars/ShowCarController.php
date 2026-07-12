<?php

namespace App\Http\Controllers\Api\Mobile\Cars;

use App\Enums\CarStatus;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\CarDetailResource;
use App\Models\Car;
use App\Services\CarService;
use Illuminate\Http\Request;

/** Returns the full detail payload of a single active car listing, incrementing its view counter. */
class ShowCarController extends BaseApiController
{
    public function __construct(private readonly CarService $carService) {}

    /** Shows an active car's details; non-active listings are hidden from the mobile app. */
    public function __invoke(Request $request, Car $car)
    {
        if ($car->status !== CarStatus::Active) {
            return $this->notFound();
        }

        $this->carService->incrementViews($car);

        $car->load([
            'brand', 'city', 'color',
            'favoritedByUser' => fn ($q) => $q->where('user_id', $request->user()?->id),
        ]);

        return $this->success(new CarDetailResource($car), __('messages.cars.detail_fetched'));
    }
}
