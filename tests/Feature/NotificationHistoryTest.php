<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\InAppNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_history_filters_all_unread_learning_and_assessment(): void
    {
        $user = User::factory()->create(['role' => 'learner']);
        $service = app(InAppNotificationService::class);

        $service->send($user, 'learning', 'Learning update', 'Continue your lesson.');
        $service->send($user, 'assessment', 'Assessment update', 'Your result is ready.');
        $service->send($user, 'general', 'General update', 'Welcome back.');

        $readNotification = $user->fresh()->notifications()->where('data->category', 'general')->firstOrFail();
        $readNotification->markAsRead();

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Notification History')
            ->assertSee('Learning update')
            ->assertSee('Assessment update')
            ->assertSee('General update');

        $this->actingAs($user)
            ->get(route('notifications.index', ['filter' => 'unread']))
            ->assertOk()
            ->assertSee('Learning update')
            ->assertSee('Assessment update')
            ->assertDontSee('General update');

        $this->actingAs($user)
            ->get(route('notifications.index', ['filter' => 'learning']))
            ->assertOk()
            ->assertSee('Learning update')
            ->assertDontSee('Assessment update')
            ->assertDontSee('General update');

        $this->actingAs($user)
            ->get(route('notifications.index', ['filter' => 'assessment']))
            ->assertOk()
            ->assertSee('Assessment update')
            ->assertDontSee('Learning update')
            ->assertDontSee('General update');
    }

    public function test_invalid_filter_falls_back_to_all_notifications(): void
    {
        $user = User::factory()->create(['role' => 'learner']);

        app(InAppNotificationService::class)->send(
            $user,
            'learning',
            'Visible update',
            'This should still be visible.',
        );

        $this->actingAs($user)
            ->get(route('notifications.index', ['filter' => 'unknown']))
            ->assertOk()
            ->assertSee('Visible update');
    }

    public function test_notification_history_requires_authentication(): void
    {
        $this->get(route('notifications.index'))
            ->assertRedirect(route('login'));
    }
}
