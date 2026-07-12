<?php

namespace App\Http\Controllers\Api\Mobile\MyListings;

use App\Enums\CarStatus;
use App\Enums\SellerType;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Lookup\PaginationRequest;
use App\Http\Resources\CarListResource;
use App\Models\Car;

/** Returns the authenticated user's own car listings across all statuses, with per-status counts. */
class MyListingsController extends BaseApiController
{
    /** Fetches the user's listings paginated, adding active/pending/sold/total counts to meta. */
    public function __invoke(PaginationRequest $request)
    {
        $base = Car::query()
            ->where('seller_type', SellerType::Individual)
            ->where('seller_id', $request->user()->id);

        $counts = [
            'active_count'  => (clone $base)->where('status', CarStatus::Active)->count(),
            'pending_count' => (clone $base)->where('status', CarStatus::Pending)->count(),
            'sold_count'    => (clone $base)->where('status', CarStatus::Sold)->count(),
            'total'         => (clone $base)->count(),
        ];

        $paginator = $base
            ->with(['brand', 'city', 'color'])
            ->latest()
            ->paginate($request->perPage());

        $paginator->setCollection(
            collect(CarListResource::collection($paginator->getCollection())->toArray($request))
        );

        return $this->successPaginated($paginator, __('messages.cars.my_listings_fetched'), $counts);
    }
}
