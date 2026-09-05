<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\InAppNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationAccessibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_history_has_accessible_structure_mobile_actions_and_keyboard_filters(): void
    {
        $user = User::factory()->create(['role' => 'learner']);

        app(InAppNotificationService::class)->send(
            $user,
            'learning',
            'Continue learning',
            'Your next lesson is ready.',
            route('dashboard'),
            'Continue Learning',
        );

        $response = $this->actingAs($user)->get(route('notifications.index'));

        $response
            ->assertOk()
            ->assertSee('data-notification-center', false)
            ->assertSee('data-notification-filters', false)
            ->assertSee('aria-labelledby="notification-history-heading"', false)
            ->assertSee('aria-live="polite"', false)
            ->assertSee('min-h-11', false)
            ->assertSee('overflow-x-auto', false)
            ->assertSee('notification-accessibility.js', false)
            ->assertSee('aria-label="Continue Learning: Continue learning"', false);
    }

    public function test_header_notification_bell_exposes_unread_state_and_keyboard_friendly_preview(): void
    {
        $user = User::factory()->create(['role' => 'learner']);

        app(InAppNotificationService::class)->send(
            $user,
            'assessment',
            'Assessment result ready',
            'Your assessment result is ready.',
            route('notifications.index'),
            'Review Result',
        );

        $response = $this->actingAs($user)->get(route('notifications.index'));

        $response
            ->assertOk()
            ->assertSee('data-notification-bell', false)
            ->assertSee('aria-haspopup="true"', false)
            ->assertSee('aria-controls="desktop-notification-menu"', false)
            ->assertSee('role="region"', false)
            ->assertSee('aria-labelledby="notification-preview-heading"', false)
            ->assertSee('1 unread notifications')
            ->assertSee('lg:hidden', false);
    }

    public function test_notification_accessibility_script_supports_arrow_home_and_end_navigation(): void
    {
        $script = file_get_contents(public_path('js/notification-accessibility.js'));

        $this->assertIsString($script);
        $this->assertStringContainsString('ArrowRight', $script);
        $this->assertStringContainsString('ArrowLeft', $script);
        $this->assertStringContainsString("event.key === 'Home'", $script);
        $this->assertStringContainsString("event.key === 'End'", $script);
        $this->assertStringContainsString('scrollIntoView', $script);
    }
}
