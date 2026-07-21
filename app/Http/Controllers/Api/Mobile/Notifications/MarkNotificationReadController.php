<?php

namespace App\Http\Controllers\Api\Mobile\Notifications;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

/** Marks a single notification as read, verifying ownership. */
class MarkNotificationReadController extends BaseApiController
{
    public function __construct(private readonly NotificationService $service)
    {
    }

    /** Marks the given notification as read. */
    public function __invoke(Request $request, Notification $notification)
    {
        $this->service->markAsRead($request->user(), $notification);

        return $this->success(null, __('messages.notifications.marked_read'));
    }
}
