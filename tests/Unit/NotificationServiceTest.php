<?php

namespace Tests\Unit;

use App\Enums\NotificationType;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies NotificationService create/list/read-state behavior. */
class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    private NotificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new NotificationService();
    }

    public function test_send_creates_a_bilingual_notification(): void
    {
        $user = User::factory()->create();

        $notification = $this->service->send($user, NotificationType::BookingConfirmed, ['booking_id' => 1]);

        $this->assertDatabaseHas('notifications', [
            'id'      => $notification->id,
            'user_id' => $user->id,
            'type'    => 'booking_confirmed',
        ]);
        $this->assertNotEmpty($notification->title_ar);
        $this->assertNotEmpty($notification->title_en);
    }

    public function test_list_returns_only_the_users_own_notifications(): void
    {
        $user  = User::factory()->create();
        $other = User::factory()->create();

        $this->service->send($user, NotificationType::CarMatch);
        $this->service->send($other, NotificationType::CarMatch);

        $paginator = $this->service->list($user, 15);

        $this->assertSame(1, $paginator->total());
    }

    public function test_mark_as_read_sets_read_at(): void
    {
        $user = User::factory()->create();
        $notification = $this->service->send($user, NotificationType::CarMatch);

        $this->service->markAsRead($user, $notification);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_mark_as_read_throws_for_non_owner(): void
    {
        $user  = User::factory()->create();
        $other = User::factory()->create();
        $notification = $this->service->send($user, NotificationType::CarMatch);

        $this->expectException(\App\Exceptions\NotOwnerException::class);

        $this->service->markAsRead($other, $notification);
    }

    public function test_mark_all_as_read_updates_every_unread_notification(): void
    {
        $user = User::factory()->create();
        $this->service->send($user, NotificationType::CarMatch);
        $this->service->send($user, NotificationType::PriceDrop);

        $this->service->markAllAsRead($user);

        $this->assertSame(0, $this->service->unreadCount($user));
    }

    public function test_unread_count_reflects_only_unread_notifications(): void
    {
        $user = User::factory()->create();
        $notification = $this->service->send($user, NotificationType::CarMatch);
        $this->service->send($user, NotificationType::PriceDrop);

        $this->service->markAsRead($user, $notification);

        $this->assertSame(1, $this->service->unreadCount($user));
    }
}
