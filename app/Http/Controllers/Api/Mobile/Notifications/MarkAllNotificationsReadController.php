<?php

namespace App\Http\Controllers\Api\Mobile\Notifications;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\NotificationService;
use Illuminate\Http\Request;

/** Marks all of the authenticated user's unread notifications as read. */
class MarkAllNotificationsReadController extends BaseApiController
{
    public function __construct(private readonly NotificationService $service)
    {
    }

    /** Marks every unread notification belonging to the user as read. */
    public function __invoke(Request $request)
    {
        $this->service->markAllAsRead($request->user());

        return $this->success(null, __('messages.notifications.all_marked_read'));
    }
}
