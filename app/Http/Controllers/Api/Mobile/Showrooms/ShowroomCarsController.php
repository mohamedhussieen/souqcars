<?php

namespace App\Http\Controllers\Api\Mobile\Showrooms;

use App\Enums\CarStatus;
use App\Enums\SellerType;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Lookup\PaginationRequest;
use App\Http\Resources\CarListResource;
use App\Models\Car;
use App\Models\Showroom;

/** Returns the active car listings belonging to a showroom, paginated. */
class ShowroomCarsController extends BaseApiController
{
    /** Fetches the showroom's active cars as list cards. */
    public function __invoke(PaginationRequest $request, Showroom $showroom)
    {
        $paginator = Car::query()
            ->where('seller_type', SellerType::Showroom)
            ->where('seller_id', $showroom->id)
            ->where('status', CarStatus::Active)
            ->with([
                'brand', 'city', 'color',
                'favoritedByUser' => fn ($q) => $q->where('user_id', $request->user()?->id),
            ])
            ->latest()
            ->paginate($request->perPage());

        $paginator->setCollection(
            collect(CarListResource::collection($paginator->getCollection())->toArray($request))
        );

        return $this->successPaginated($paginator, __('messages.cars.fetched'));
    }
}
