<?php

namespace App\Http\Controllers\Api\Admin\CarModels;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\AdminCarModelListRequest;
use App\Http\Resources\Admin\AdminCarModelResource;
use App\Services\AdminCarModelService;

/** Returns a paginated, optionally brand-filtered list of car models for the admin dashboard. */
class ListCarModelsController extends BaseApiController
{
    public function __construct(private readonly AdminCarModelService $adminCarModelService) {}

    /** Fetches car models paginated via AdminCarModelService. */
    public function __invoke(AdminCarModelListRequest $request)
    {
        $paginator = $this->adminCarModelService->list(
            $request->perPage(),
            $request->input('brand_id')
        );

        $paginator->setCollection(
            collect(AdminCarModelResource::collection($paginator->getCollection())->toArray($request))
        );

        return $this->successPaginated($paginator, __('messages.admin.car_models_fetched'));
    }
}
