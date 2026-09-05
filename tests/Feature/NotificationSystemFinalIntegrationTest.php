<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\InAppNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationSystemFinalIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_learner_can_use_notification_center_end_to_end(): void
    {
        $user = User::factory()->create(['role' => 'learner']);
        $service = app(InAppNotificationService::class);

        $service->send(
            $user,
            category: 'learning',
            title: 'Continue your lesson',
            message: 'Your next lesson is ready.',
            url: route('dashboard'),
            actionLabel: 'Continue Learning',
        );

        $service->send(
            $user,
            category: 'assessment',
            title: 'Assessment result ready',
            message: 'Review your latest result.',
            url: route('assessment-performance'),
            actionLabel: 'Review Result',
        );

        $response = $this->actingAs($user)->get(route('notifications.index'));

        $response
            ->assertOk()
            ->assertSee('Notification History')
            ->assertSee('Continue your lesson')
            ->assertSee('Assessment result ready')
            ->assertSee('All')
            ->assertSee('Unread')
            ->assertSee('Learning')
            ->assertSee('Assessment')
            ->assertSee('Preferences')
            ->assertSee('data-notification-center', false)
            ->assertSee('data-notification-filters', false)
            ->assertSee('notification-accessibility.js', false);

        $this->assertSame(2, $user->fresh()->unreadNotifications()->count());
    }

    public function test_notification_filters_and_read_actions_work_together(): void
    {
        $user = User::factory()->create(['role' => 'learner']);
        $service = app(InAppNotificationService::class);

        $service->send($user, 'learning', 'Learning update', 'Continue your course.', route('dashboard'));
        $service->send($user, 'assessment', 'Assessment update', 'Review your result.', route('assessment-performance'));

        $this->actingAs($user)
            ->get(route('notifications.index', ['filter' => 'learning']))
            ->assertOk()
            ->assertSee('Learning update')
            ->assertDontSee('Assessment update');

        $notification = $user->fresh()->unreadNotifications()->where('data->category', 'learning')->firstOrFail();

        $this->actingAs($user)
            ->patch(route('notifications.read', $notification->id))
            ->assertRedirect(route('dashboard'));

        $this->assertNotNull($notification->fresh()->read_at);

        $this->actingAs($user)
            ->get(route('notifications.index', ['filter' => 'unread']))
            ->assertOk()
            ->assertDontSee('Learning update')
            ->assertSee('Assessment update');
    }

    public function test_notification_preferences_are_enforced_across_the_system(): void
    {
        $user = User::factory()->create([
            'role' => 'learner',
            'notification_preferences' => [
                'enabled' => true,
                'learning' => false,
                'assessment' => true,
                'milestone' => false,
                'general' => true,
            ],
        ]);

        $service = app(InAppNotificationService::class);

        $this->assertFalse($service->send($user, 'learning', 'Learning', 'Hidden'));
        $this->assertTrue($service->send($user, 'assessment', 'Assessment', 'Visible'));
        $this->assertFalse($service->send($user, 'milestone', 'Milestone', 'Hidden'));
        $this->assertTrue($service->send($user, 'general', 'General', 'Visible'));

        $this->assertSame(2, $user->fresh()->notifications()->count());
    }

    public function test_notification_routes_require_authentication(): void
    {
        $this->get(route('notifications.index'))->assertRedirect(route('login'));
        $this->get(route('profile.notifications'))->assertRedirect(route('login'));
        $this->patch(route('notifications.read-all'))->assertRedirect(route('login'));
        $this->patch(route('profile.notifications.update'))->assertRedirect(route('login'));
    }

    public function test_header_and_mobile_notification_entry_points_are_present(): void
    {
        $user = User::factory()->create(['role' => 'learner']);
        app(InAppNotificationService::class)->send(
            $user,
            'general',
            'Welcome back',
            'You have a new SignGyaan update.',
            route('dashboard'),
            'Open Dashboard',
        );

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertSee('data-notification-bell', false)
            ->assertSee('aria-controls="desktop-notification-menu"', false)
            ->assertSee('Notifications, 1 unread', false)
            ->assertSee('lg:hidden', false);
    }
}
