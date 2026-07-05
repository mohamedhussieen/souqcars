<?php

namespace App\Services;

use App\Http\Resources\BrandResource;
use App\Http\Resources\CarModelResource;
use App\Http\Resources\CityResource;
use App\Models\Brand;
use App\Models\City;
use Illuminate\Pagination\LengthAwarePaginator;

/** Handles all lookup data retrieval for cities, brands, and car models. */
class LookupService
{
    /** Returns a paginated collection of cities. */
    public function getCities(int $perPage): LengthAwarePaginator
    {
        $paginator = City::paginate($perPage);

        return $this->wrapWithResource($paginator, CityResource::class);
    }

    /** Returns a paginated collection of car brands. */
    public function getBrands(int $perPage): LengthAwarePaginator
    {
        $paginator = Brand::paginate($perPage);

        return $this->wrapWithResource($paginator, BrandResource::class);
    }

    /** Returns a paginated collection of car models belonging to the given brand. */
    public function getModelsByBrand(Brand $brand, int $perPage): LengthAwarePaginator
    {
        $paginator = $brand->carModels()->paginate($perPage);

        return $this->wrapWithResource($paginator, CarModelResource::class);
    }

    /**
     * Wraps a LengthAwarePaginator's items through an API Resource collection,
     * replacing raw Eloquent items with resource instances while preserving pagination state.
     */
    private function wrapWithResource(LengthAwarePaginator $paginator, string $resourceClass): LengthAwarePaginator
    {
        $resourceCollection = $resourceClass::collection($paginator->getCollection());

        return $paginator->setCollection(
            collect($resourceCollection->toArray(request()))
        );
    }
}
