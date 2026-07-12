<?php

namespace App\Http\Controllers\Api\Admin\Cars;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\AdminCarListRequest;
use App\Http\Resources\Admin\CarAdminResource;
use App\Models\Car;
use App\Services\CarFilterService;

/** Returns all car listings (any status, any seller) for the admin dashboard, filtered and paginated. */
class ListCarsController extends BaseApiController
{
    public function __construct(private readonly CarFilterService $carFilterService) {}

    /** Applies admin filters (status, seller_type, brand, city, search) and returns paginated rows. */
    public function __invoke(AdminCarListRequest $request)
    {
        $query = Car::query()
            ->when($request->input('status'), fn ($q, $v) => $q->where('status', $v))
            ->with(['brand', 'city', 'color']);

        $this->carFilterService->apply($query, $request->filters());

        $paginator = $query->paginate($request->perPage());

        $paginator->setCollection(
            collect(CarAdminResource::collection($paginator->getCollection())->toArray($request))
        );

        return $this->successPaginated($paginator, __('messages.admin.cars_fetched'));
    }
}
