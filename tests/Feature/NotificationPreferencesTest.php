<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\InAppNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationPreferencesTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_preferences_page_requires_authentication(): void
    {
        $this->get(route('profile.notifications'))
            ->assertRedirect(route('login'));
    }

    public function test_learner_can_update_notification_preferences(): void
    {
        $user = User::factory()->create(['role' => 'learner']);

        $this->actingAs($user)
            ->patch(route('profile.notifications.update'), [
                'enabled' => '1',
                'learning' => '1',
                'assessment' => '0',
                'milestone' => '1',
                'general' => '0',
            ])
            ->assertRedirect()
            ->assertSessionHas('notification_status');

        $preferences = $user->fresh()->notification_preferences;

        $this->assertTrue($preferences['enabled']);
        $this->assertTrue($preferences['learning']);
        $this->assertFalse($preferences['assessment']);
        $this->assertTrue($preferences['milestone']);
        $this->assertFalse($preferences['general']);
    }

    public function test_disabled_category_prevents_new_notifications(): void
    {
        $user = User::factory()->create([
            'role' => 'learner',
            'notification_preferences' => [
                'enabled' => true,
                'learning' => false,
                'assessment' => true,
                'milestone' => true,
                'general' => true,
            ],
        ]);

        $sent = app(InAppNotificationService::class)->send(
            $user,
            category: 'learning',
            title: 'Continue learning',
            message: 'A learning update.',
        );

        $this->assertFalse($sent);
        $this->assertDatabaseCount('notifications', 0);
    }

    public function test_master_switch_prevents_all_new_notifications(): void
    {
        $user = User::factory()->create([
            'role' => 'learner',
            'notification_preferences' => [
                'enabled' => false,
                'learning' => true,
                'assessment' => true,
                'milestone' => true,
                'general' => true,
            ],
        ]);

        $service = app(InAppNotificationService::class);

        $this->assertFalse($service->send($user, 'learning', 'Learning', 'Message'));
        $this->assertFalse($service->send($user, 'assessment', 'Assessment', 'Message'));
        $this->assertDatabaseCount('notifications', 0);
    }

    public function test_existing_users_default_to_receiving_notifications(): void
    {
        $user = User::factory()->create([
            'role' => 'learner',
            'notification_preferences' => null,
        ]);

        $sent = app(InAppNotificationService::class)->send(
            $user,
            category: 'learning',
            title: 'Next lesson ready',
            message: 'Continue your course.',
        );

        $this->assertTrue($sent);
        $this->assertDatabaseCount('notifications', 1);
    }
}
