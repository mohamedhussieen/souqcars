<?php

namespace App\Traits;

use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

/** Adds push-notification sending to a model that owns UserFcmToken records (e.g. User). */
trait SendsFirebaseNotifications
{
    /**
     * Sends a push notification to every device token registered for this model.
     * Automatically deletes tokens that Firebase reports as unregistered or invalid.
     *
     * @param array<string, string> $data Custom key-value payload delivered alongside the notification.
     */
    public function sendPushNotification(string $title, string $body, array $data = []): void
    {
        if (!$this->notification_enabled) {
            return;
        }

        $tokens = $this->fcmTokens()->pluck('token')->all();

        if (empty($tokens)) {
            return;
        }

        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $body))
            ->withData($data);

        $report = app(Messaging::class)->sendMulticast($message, $tokens);

        $deadTokens = [...$report->unknownTokens(), ...$report->invalidTokens()];

        if (!empty($deadTokens)) {
            $this->fcmTokens()->whereIn('token', $deadTokens)->delete();
        }
    }
}
