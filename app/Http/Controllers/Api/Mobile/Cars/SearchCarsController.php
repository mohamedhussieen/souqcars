<?php

namespace App\Http\Controllers\Api\Mobile\Cars;

use App\Enums\CarStatus;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Mobile\CarSearchRequest;
use App\Http\Resources\CarListResource;
use App\Models\Car;
use App\Services\CarFilterService;

/** Searches active car listings by title with all standard filters available. */
class SearchCarsController extends BaseApiController
{
    public function __construct(private readonly CarFilterService $carFilterService) {}

    /** Runs a required-term search over active cars and returns paginated list cards. */
    public function __invoke(CarSearchRequest $request)
    {
        $query = Car::query()
            ->where('status', CarStatus::Active)
            ->with([
                'brand', 'city', 'color',
                'favoritedByUser' => fn ($q) => $q->where('user_id', $request->user()?->id),
            ]);

        $this->carFilterService->apply($query, $request->filters());

        $paginator = $query->paginate($request->perPage());

        $paginator->setCollection(
            collect(CarListResource::collection($paginator->getCollection())->toArray($request))
        );

        return $this->successPaginated($paginator, __('messages.cars.fetched'));
    }
}
