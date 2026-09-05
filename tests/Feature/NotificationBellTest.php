<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\InAppNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationBellTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_learner_sees_notification_bell_unread_count_and_preview(): void
    {
        $user = User::factory()->create(['role' => 'learner']);
        $service = app(InAppNotificationService::class);

        $service->send(
            $user,
            'learning',
            'Continue Computer Basics',
            'Your next lesson is ready.',
            route('dashboard'),
            'Continue Learning',
        );

        $service->send(
            $user,
            'assessment',
            'Assessment result ready',
            'Review your latest result.',
            route('assessment-performance'),
            'Review Result',
        );

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('data-notification-bell', false)
            ->assertSee('aria-controls="desktop-notification-menu"', false)
            ->assertSee('Notifications, 2 unread')
            ->assertSee('Continue Computer Basics')
            ->assertSee('Assessment result ready')
            ->assertSee('View all notifications')
            ->assertSee(route('notifications.index'), false);
    }

    public function test_read_notifications_do_not_appear_in_unread_bell_preview(): void
    {
        $user = User::factory()->create(['role' => 'learner']);
        $service = app(InAppNotificationService::class);

        $service->send($user, 'learning', 'Unread lesson update', 'Keep learning.');
        $service->send($user, 'general', 'Old notification', 'Already reviewed.');

        $old = $user->fresh()->notifications()->where('data->title', 'Old notification')->first();
        $old?->markAsRead();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Notifications, 1 unread')
            ->assertSee('Unread lesson update')
            ->assertDontSee('Old notification');
    }

    public function test_guest_header_does_not_render_notification_bell(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('data-notification-bell', false)
            ->assertDontSee('desktop-notification-menu', false);
    }
}
