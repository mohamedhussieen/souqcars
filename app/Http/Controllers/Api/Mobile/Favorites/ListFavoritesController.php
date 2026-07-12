<?php

namespace App\Http\Controllers\Api\Mobile\Favorites;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Lookup\PaginationRequest;
use App\Http\Resources\FavoriteResource;
use App\Services\FavoriteService;

/** Returns the authenticated user's favorited cars, paginated. */
class ListFavoritesController extends BaseApiController
{
    public function __construct(private readonly FavoriteService $favoriteService) {}

    /** Fetches the user's favorites paginated via FavoriteService. */
    public function __invoke(PaginationRequest $request)
    {
        $paginator = $this->favoriteService->list($request->user(), $request->perPage());

        $paginator->setCollection(
            collect(FavoriteResource::collection($paginator->getCollection())->toArray($request))
        );

        return $this->successPaginated($paginator, __('messages.favorites.fetched'));
    }
}
