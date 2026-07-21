<?php

namespace App\Http\Controllers\Api\Mobile\Notifications;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Lookup\PaginationRequest;
use App\Http\Resources\NotificationResource;
use App\Services\NotificationService;

/** Returns a paginated list of the authenticated user's notifications, including the unread count in meta. */
class ListNotificationsController extends BaseApiController
{
    public function __construct(private readonly NotificationService $service)
    {
    }

    /** Fetches the user's notifications, newest first, with unread_count in the response meta. */
    public function __invoke(PaginationRequest $request)
    {
        $paginator = $this->service->list($request->user(), $request->perPage());

        $paginator->setCollection(
            collect(NotificationResource::collection($paginator->getCollection())->toArray($request))
        );

        return $this->successPaginated($paginator, __('messages.notifications.fetched'), [
            'unread_count' => $this->service->unreadCount($request->user()),
        ]);
    }
}
