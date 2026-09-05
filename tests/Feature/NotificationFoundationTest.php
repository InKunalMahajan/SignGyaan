<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\InAppNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_service_stores_database_notification_and_center_displays_it(): void
    {
        $user = User::factory()->create(['role' => 'learner']);

        app(InAppNotificationService::class)->send(
            $user,
            category: 'learning',
            title: 'Continue your lesson',
            message: 'Your Computer Basics lesson is ready to continue.',
            url: route('dashboard'),
            actionLabel: 'Continue Learning',
            meta: ['course_slug' => 'computer-basics'],
        );

        $this->assertDatabaseCount('notifications', 1);
        $this->assertSame(1, $user->fresh()->unreadNotifications()->count());

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Notifications')
            ->assertSee('Continue your lesson')
            ->assertSee('Your Computer Basics lesson is ready to continue.')
            ->assertSee('Continue Learning')
            ->assertSee('Unread');
    }

    public function test_learner_can_mark_one_notification_as_read_and_follow_its_destination(): void
    {
        $user = User::factory()->create(['role' => 'learner']);

        app(InAppNotificationService::class)->send(
            $user,
            'general',
            'Welcome back',
            'Open your dashboard to continue learning.',
            route('dashboard'),
            'Open Dashboard',
        );

        $notification = $user->fresh()->unreadNotifications()->firstOrFail();

        $this->actingAs($user)
            ->patch(route('notifications.read', $notification->id))
            ->assertRedirect(route('dashboard'));

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_learner_can_mark_all_notifications_as_read(): void
    {
        $user = User::factory()->create(['role' => 'learner']);
        $service = app(InAppNotificationService::class);

        $service->send($user, 'learning', 'First update', 'First message');
        $service->send($user, 'assessment', 'Second update', 'Second message');

        $this->assertSame(2, $user->fresh()->unreadNotifications()->count());

        $this->actingAs($user)
            ->patch(route('notifications.read-all'))
            ->assertRedirect();

        $this->assertSame(0, $user->fresh()->unreadNotifications()->count());
    }

    public function test_notification_access_is_scoped_to_authenticated_user(): void
    {
        $owner = User::factory()->create(['role' => 'learner']);
        $otherUser = User::factory()->create(['role' => 'learner']);

        app(InAppNotificationService::class)->send(
            $owner,
            'learning',
            'Private update',
            'Only the owner should be able to open this notification.',
        );

        $notification = $owner->fresh()->notifications()->firstOrFail();

        $this->actingAs($otherUser)
            ->patch(route('notifications.read', $notification->id))
            ->assertNotFound();

        $this->assertNull($notification->fresh()->read_at);
    }

    public function test_notification_center_requires_authentication(): void
    {
        $this->get(route('notifications.index'))
            ->assertRedirect(route('login'));
    }
}
