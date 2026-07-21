<?php

namespace App\Services;

use App\Enums\NotificationType;
use App\Exceptions\NotOwnerException;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

/** Creates, lists, and manages read state for in-app notifications, mirroring every send to FCM push. */
class NotificationService
{
    /**
     * Creates a notification for the given user and fires a push notification.
     * Title/body are resolved from the notification type's translation keys; $data is stored as-is
     * and forwarded as the FCM data payload.
     */
    public function send(User $user, NotificationType $type, array $data = []): Notification
    {
        $notification = Notification::create([
            'user_id'  => $user->id,
            'type'     => $type->value,
            'title_ar' => __("messages.notifications.{$type->value}_title", [], 'ar'),
            'title_en' => __("messages.notifications.{$type->value}_title", [], 'en'),
            'body_ar'  => __("messages.notifications.{$type->value}_body", [], 'ar'),
            'body_en'  => __("messages.notifications.{$type->value}_body", [], 'en'),
            'data'     => $data,
        ]);

        $user->sendPushNotification(
            $notification->title_en,
            $notification->body_en,
            array_map('strval', $data),
        );

        return $notification;
    }

    /** Returns a paginated list of the user's notifications, newest first. */
    public function list(User $user, int $perPage): LengthAwarePaginator
    {
        return Notification::query()
            ->where('user_id', $user->id)
            ->latest()
            ->paginate($perPage);
    }

    /** Marks a single notification as read. Throws NotOwnerException if the user doesn't own it. */
    public function markAsRead(User $user, Notification $notification): void
    {
        if ($notification->user_id !== $user->id) {
            throw new NotOwnerException('messages.notifications.forbidden');
        }

        if ($notification->read_at === null) {
            $notification->update(['read_at' => now()]);
        }
    }

    /** Marks all of the user's unread notifications as read. */
    public function markAllAsRead(User $user): void
    {
        Notification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /** Returns the user's unread notification count. */
    public function unreadCount(User $user): int
    {
        return Notification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();
    }
}
