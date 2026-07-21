<?php

namespace App\Http\Controllers\Api\Mobile\Notifications;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\NotificationService;
use Illuminate\Http\Request;

/** Returns the authenticated user's unread notification count. */
class UnreadNotificationCountController extends BaseApiController
{
    public function __construct(private readonly NotificationService $service)
    {
    }

    /** Fetches the current unread count. */
    public function __invoke(Request $request)
    {
        return $this->success(['count' => $this->service->unreadCount($request->user())], __('messages.notifications.unread_count'));
    }
}
