<?php

namespace Tests\Feature\Mobile;

use App\Enums\NotificationType;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies the mobile notifications list/read/read-all/unread-count endpoints. */
class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private function tokenFor(User $user): string
    {
        return $user->createToken('mobile-app')->plainTextToken;
    }

    public function test_list_notifications_is_paginated_with_unread_count_in_meta(): void
    {
        $user = User::factory()->create();
        app(NotificationService::class)->send($user, NotificationType::CarMatch);
        app(NotificationService::class)->send($user, NotificationType::PriceDrop);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->getJson('/api/v1/mobile/notifications');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
        $this->assertSame(2, $response->json('meta.unread_count'));
    }

    public function test_mark_single_notification_as_read(): void
    {
        $user = User::factory()->create();
        $notification = app(NotificationService::class)->send($user, NotificationType::CarMatch);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->putJson("/api/v1/mobile/notifications/{$notification->id}/read");

        $response->assertStatus(200);
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_mark_all_notifications_as_read(): void
    {
        $user = User::factory()->create();
        app(NotificationService::class)->send($user, NotificationType::CarMatch);
        app(NotificationService::class)->send($user, NotificationType::PriceDrop);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->putJson('/api/v1/mobile/notifications/read-all');

        $response->assertStatus(200);
        $this->assertDatabaseCount('notifications', 2);
        $this->assertSame(0, $user->notifications()->whereNull('read_at')->count());
    }

    public function test_unread_count_is_correct(): void
    {
        $user = User::factory()->create();
        $notification = app(NotificationService::class)->send($user, NotificationType::CarMatch);
        app(NotificationService::class)->send($user, NotificationType::PriceDrop);
        app(NotificationService::class)->markAsRead($user, $notification);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->getJson('/api/v1/mobile/notifications/unread-count');

        $response->assertStatus(200)->assertJsonPath('data.count', 1);
    }
}
